<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Auth;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function side_change(Request $request)
    {
        session(['groom_side' => $request->groom_side]);
        return back();
    }

    public function general_settings()
    {
        $general_settings = General_setting::all();
        $data = array();
        foreach($general_settings as $general_setting) {
            $data[$general_setting->setting_key] = $general_setting->setting_val;
        }
        return view('admin.general_settings',$data);
    }

    public function submit_general_settings(Request $request)
    {
        try {
            $post = $request->all();
            unset($post['_token']);
            
            $data = array();
            foreach($post as $key => $val) {
                $data[] = ['setting_key' => $key,'setting_val' => $val,"created_at" => date("Y-m-d H:i:s")];
            }
            $logo = $this->uploadImage($request,'logo','uploads/banner',$post['old_logo'] ?? null);
            $data[] = ['setting_key' => 'logo','setting_val' => $logo,'created_at'  => now()];

            $bannerImage1 = $this->uploadImage($request,'banner_image_1','uploads/banner',$post['old_banner_image_1'] ?? null);
            $data[] = ['setting_key' => 'banner_image_1','setting_val' => $bannerImage1,'created_at'  => now()];

            $bannerImage2 = $this->uploadImage($request,'banner_image_2','uploads/banner',$post['old_banner_image_2'] ?? null);
            $data[] = ['setting_key' => 'banner_image_2','setting_val' => $bannerImage2,'created_at'  => now()];

            $bannerImage3 = $this->uploadImage($request,'banner_image_3','uploads/banner',$post['old_banner_image_3'] ?? null);
            $data[] = ['setting_key' => 'banner_image_3','setting_val' => $bannerImage3,'created_at'  => now()];

            $about_image = $this->uploadImage($request,'about_image','uploads/banner',$post['old_about_image'] ?? null);
            $data[] = ['setting_key' => 'about_image','setting_val' => $about_image,'created_at'  => now()];

            $highlight_image = $this->uploadImage($request,'highlight_image','uploads/banner',$post['old_highlight_image'] ?? null);
            $data[] = ['setting_key' => 'highlight_image','setting_val' => $highlight_image,'created_at'  => now()];

            $business_image = $this->uploadImage($request,'business_image','uploads/banner',$post['old_business_image'] ?? null);
            $data[] = ['setting_key' => 'business_image','setting_val' => $business_image,'created_at'  => now()];

            General_setting::truncate();
            General_setting::insert($data);

            $settings = General_setting::where('setting_val', 'LIKE', '%tmp')->orderBy('id', 'DESC')->get();
            if(!$settings->isEmpty()) {
                foreach($settings as $setting) {
                    $row = General_setting::find($setting['id']);
                    $row->delete();
                }
            }
            $old_settings = General_setting::where('setting_key', 'LIKE', 'old_%')->orderBy('id', 'DESC')->get();
            if(!$old_settings->isEmpty()) {
                foreach($old_settings as $old_setting) {
                    $row = General_setting::find($old_setting['id']);
                    $row->delete();
                }
            }
            return response()->json(['success' => true,'message' => "General Settings updated."], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage()], 200);
        }
    }

    private function uploadImage(Request $request, $fieldName, $folder, $oldFile = null)
    {
        if ($request->hasFile($fieldName) && $request->file($fieldName)->isValid()) {
            $file = $request->file($fieldName);
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();

            $destination = public_path($folder);
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);

            if (!empty($oldFile) && file_exists($destination . '/' . $oldFile)) {
                unlink($destination . '/' . $oldFile);
            }
            return $filename;
        }
        return $oldFile;
    }

    public function inquiries()
    {
        return view('admin.inquiry');
    }

    public function load_inquiries(Request $request)
    {
        try {
            $draw = intval($request->get('draw', 0));
            $start = intval($request->get('start', 0));
            $length = intval($request->get('length', 10));
            $searchValue = $request->input('search.value', '');

            $query = Inquiry::query();
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%");
                    $q->where('email', 'like', "%{$searchValue}%");
                    $q->where('phone', 'like', "%{$searchValue}%");
                });
            }
            $recordsTotal = Inquiry::count();
            $recordsFiltered = $query->count();
            $rows = $query->offset($start)->limit($length)->orderBy('id', 'desc')->get();

            $formattedData = [];
            foreach ($rows as $index => $row) {
                $actions = '<div class="edit-delete-action">';
                $actions .= "<a href=\"" . route('admin.remove.inquiry', ['id' => $row->id]) . "\" onclick=\"return confirm('Are you sure?')\">Delete</a>";
                $formattedData[] = [
                    'id' => $start + $index + 1,
                    'name' => $row->name,
                    'email' => $row->email,
                    'phone' => $row->phone,
                    'message' => $row->message,
                    'location' => $row->location,
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

    public function remove_inquiry($id)
    {
        Inquiry::destroy($id);
        return redirect()->route("admin.inquiry");
    }
}
