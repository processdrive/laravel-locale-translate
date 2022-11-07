<?php

namespace ProcessDrive\LaravelFileTranslate\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Hms2Go\Http\Controllers\Controller;
use ProcessDrive\LaravelFileTranslate\CloudTranslate;
use ProcessDrive\LaravelFileTranslate\jobs\MakeNewLocale;
use DB;
use DataTables;


class LaravelFileTranslateController extends Controller
{
   public function index ()
   {
      $data['language'] = DB::table('translate_language_isocode')->where('used', 1)->get()->pluck('name', 'iso_code')->toArray();
      $data['new_lang'] = DB::table('translate_language_isocode')->where('used', 0)->get()->pluck('name', 'iso_code')->toArray();
      $data['load']     = count(DB::table('translation_load')->get()->toArray()) ? true : false;
      return view('LaravelFileTranslate::master')->with($data);
   }

   public function store(Request $request)
   {
        $request_data  = $request->all();
        $locale        = DB::table('translate_language_isocode')->where('used', 1)->get()->pluck('iso_code')->toArray();
        $translate     = new CloudTranslate();
        if ($request_data['text']) {
            foreach ($locale as $lang) {
                $request_data[$lang] = $translate->translate($request_data['text'], $this->checkLang($request_data['lang']), $this->checkLang($lang));
            }
        }
        unset($request_data['lang']);
        unset($request_data['text']);
        DB::table('translation_db')->insert($request_data);
        return true;
   }

   public function update(Request $request)
   {
        $request_data  = $request->all();
        DB::table('translation_db')->where('id',$request->get('edit_id'))->update([$request_data['lang'] => $request_data['text']]);
        return true;
   }

   public function destory(Request $request)
   {
        return DB::table('translation_db')->delete($request->get('id'));
   }

   public function getTranslation(Request $request)
   {
      if ($request->ajax()) {
         $language = $request->get('lang');
         $data =  DB::table('translation_db')->select('id', 'group', 'key', $language)->where($language, '!=', null)->get();
         return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                  $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm" data-attr="'.$row->id.'"><i class="fa fa-pencil" aria-hidden="true"></i></a> 
                              <a href="javascript:void(0)" class="delete btn btn-danger btn-sm" data-attr="'.$row->id.'"><i class="fa fa-trash" aria-hidden="true"></i></a>
                              <a href="javascript:void(0)" class="update btn btn-primary btn-sm" data-attr="'.$row->id.'" style="display: none;"><i class="fa fa-save"></i></a>
                              <a href="javascript:void(0)" class="cancel btn btn-danger btn-sm" style="display: none;"><i class="fa fa-close"></i></a>';
                  return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
      }
   }

   public function storeNewLanguage(Request $request)
   {
      DB::table('translation_load')->insert(['name' => 'new_lang', 'value' => 1]);
      MakeNewLocale::dispatch($request->all())->delay(now()->addSeconds(1));
      return true;
   }

   public function checkLang($locale)
   {
      $iso_code = DB::table('translate_language_isocode')->get()->pluck('iso_code')->toArray();
      return array_search($locale, $iso_code) ? $locale : 'en'; 
   }

}
