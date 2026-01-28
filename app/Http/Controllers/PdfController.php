<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class PdfController extends Controller
{
    public function index()
    {
        return view('pdf.list');
    }

    public function load(Request $request)
    {
        try {
            $draw = intval($request->get('draw', 0));
            $start = intval($request->get('start', 0));
            $length = intval($request->get('length', 10));
            $searchValue = $request->input('search.value', '');

            $query = Pdf::query();
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%");
                });
            }
            $recordsTotal = Pdf::count();
            $recordsFiltered = $query->count();
            $rows = $query->offset($start)->limit($length)->orderBy('id', 'desc')->get();

            $formattedData = [];
            foreach ($rows as $index => $row) {
                $actions = '<div class="edit-delete-action">';
                    $actions .= '<a href="' . url('pdfs/'.$row->id) . '" class="me-2 edit-icon p-2" title="Download">Download</a>';
                    $actions .= '<a href="'.url('pdfs/remove/'.$row->id).'"onclick="return confirm(\'Are you sure?\')" class="p-2" title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>';
                $actions .= '</div>';
                $formattedData[] = [
                    'id' => $start + $index + 1,
                    'name' => $row->name,
                    'actions' => $actions
                ];
            }
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $formattedData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function create()
    {
        $pdf = null;
        return view('pdf.add_edit',compact('pdf'));
    }

    public function store(Request $request)
    {
        try {
            $post = $request->all();
            $name = time();

            $pdf_file = "";
            if ($request->hasFile('pdf')) {
                $pdf = $request->file('pdf');

                // generate random file name
                $pdf_file = Str::random(20) . '.' . $pdf->getClientOriginalExtension();
                $path = $pdf->move(public_path('uploads/files'), $pdf_file);
            }

            $xlsx_file = "";
            if ($request->hasFile('xlsx')) {
                $xlsx = $request->file('xlsx');

                // generate random file name
                $xlsx_file = Str::random(20) . '.' . $xlsx->getClientOriginalExtension();
                $path = $xlsx->move(public_path('uploads/files'), $xlsx_file);
            }

            $row = new Pdf;
            $row->name = $name;
            $row->pdf_file = $pdf_file;
            $row->xlsx_file = $xlsx_file;
            $row->created_at = date("Y-m-d H:i:s");
            $row->save();

            $file = $pdf_file;
            $source = public_path('uploads/files/' . $file);
            $outputFolder = public_path('uploads/' . $name);

            // 1️⃣ Create output folder if not exists
            if (!File::exists($outputFolder)) {
                File::makeDirectory($outputFolder, 0755, true);
            }

            // 2️⃣ Get last page number
            $files = glob($outputFolder . '/page_*.pdf');
            natsort($files);

            $no = 0;
            if (!empty($files)) {
                $lastFile = end($files);
                if (preg_match('/page_(\d+)\.pdf/', $lastFile, $matches)) {
                    $no = (int) $matches[1];
                }
            }

            // 3️⃣ Split PDF
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($source);

            for ($i = 1; $i <= $pageCount; $i++) {
                $no++;

                $newPdf = new Fpdi();
                $newPdf->AddPage();
                $newPdf->setSourceFile($source);

                $templateId = $newPdf->importPage($i);
                $newPdf->useTemplate($templateId);

                $outputName = $outputFolder . '/page_' . $no . '.pdf';
                $newPdf->Output($outputName, 'F');
            }
            File::delete($source);

            // rename files
            $excelFile = public_path('uploads/files/'.$xlsx_file);
            $folder = public_path('uploads/'.$name.'/');

            // Check Excel file
            if (!File::exists($excelFile)) {
                return response()->json(['error' => 'Excel file not found'], 404);
            }

            // Load Excel
            $spreadsheet = IOFactory::load($excelFile);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Remove header row
            array_shift($rows);

            $serial = 1;
            $log = [];

            foreach ($rows as $row) {
                $loanNo = trim($row[0] ?? '');
                echo $loanNo;

                if ($loanNo !== '') {
                    $oldFile = $folder . "page_" . $serial . ".pdf";
                    $newFile = $folder . $loanNo . ".pdf";

                    if (File::exists($oldFile)) {
                        if (!File::exists($newFile)) {
                            File::move($oldFile, $newFile);
                            // echo "Renamed: page_$serial.pdf → $loanNo.pdf<br>";
                        } else {
                            // echo "Skipped (already exists): $loanNo.pdf<br>";
                        }
                    } else {
                        // echo "File not found: page_$serial.pdf<br>";
                    }
                }
                $serial++;
                exit;
            }
            
            return response()->json(['success' => true,'message' => $pageCount." files has been created."], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage()], 200);
        }
    }

    public function show($id)
    {
        $row = Pdf::find($id);
        if(!$row) {
            return redirect("pdfs");
        }
        $folderPath = public_path('uploads/' . $row->name);
        $zipFileName = $row->name . '.zip';
        $zipPath = public_path('uploads/' . $zipFileName);

        // Check folder exists
        if (!File::exists($folderPath)) {
            return back()->with('error', 'PDF folder not found.');
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {

            // Get all PDFs
            $files = File::files($folderPath);

            foreach ($files as $file) {
                if ($file->getExtension() === 'pdf') {
                    $zip->addFile(
                        $file->getRealPath(),
                        $file->getFilename()
                    );
                }
            }

            $zip->close();
        } else {
            return back()->with('error', 'Could not create ZIP file.');
        }
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function pdf_remove($id)
    {
        $row = Pdf::find($id);
        if(!$row) {
            return redirect("pdfs");
        }
        $folderPath = public_path('uploads/' . $row->name);

        if (File::exists($folderPath)) {
            File::deleteDirectory($folderPath); // deletes folder + all files
        }

        // optional: delete DB record
        $row->delete();

        return redirect('pdfs')->with('success', 'Folder deleted successfully'); 
    }
}
