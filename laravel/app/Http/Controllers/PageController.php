<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;


class PageController extends Controller
{
     public function index(Request $request, Page $page)
    {
        if ($request->ajax()) {

            $pages = $page->getAllpages();

            return datatables()->of($pages)
                ->addIndexColumn()
                 ->addColumn('page_type', function ($page) {
                    return $page->getPageType();
                })
               
                ->addColumn('created_at', function ($page) {
                    return $page->created_at;
                })

                ->addColumn('action', function ($page) {
                $btn = '';
                $btn = '<a href="' . route('page.show',encrypt($page->id)) . '" title="View"><i class="fas fa-eye mr-1"></i></a>&nbsp;&nbsp;';
                 $btn .= '<a href="' . route('page.edit',encrypt($page->id)) . '" title="Edit"><i class="fas fa-edit ml-1"></i></a>&nbsp;&nbsp;';

                $btn .= '<a href="javascript:void(0);" delete_form="delete_customer_form"  data-id="' .$page->id. '" class="delete-datatable-record text-danger delete-pages-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

                return $btn;
            })
                ->rawColumns([
                'action'
                
            ])
                ->make(true);
        }

        return view('page.index');
    }

     public function create()
    {
    	$pageTypes = Page::getPageTypeOptions();
        return view('page.create',compact("pageTypes"));
    }

     public function store(Request $request)
    {

        $rules = array(
            'title' => 'required',
            'description' => 'required',
            'page_type' => 'required|unique:pages,page_type',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } 
        $page= new Page;
        $page = $page->fill($request->all());
       
        if ($page->save()) {
            return redirect('/page')->with('success', "Page added successfully");
        } else {
            return Redirect::back()->withInput()->with('error', 'Some error occured. Please try again later');
        }
        
    }

     public function edit($id)
    {
        $model = Page::find(decrypt($id));
        if(!$model){
            return redirect()->back()->with('error', 'This page does not exist');
        }
        $pageTypes = Page::getPageTypeOptions();
        return view('page.edit',compact("model","pageTypes"));
    }

     public function show($id)
    {
        $model = Page::find(decrypt($id));
        if(empty($model))
            return redirect('/page')->back()->with('error', 'This page does not exist');

        return view('page.view',['model'=>$model]);
    }

    public function update($id,Request $request){

         $rules = array(
            'title' => 'required',
            'description' => 'required',
            'page_type' => 'required|unique:pages,page_type,'.$id,
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $model = Page::find($id);

        $model = $model->fill($request->all());

        if ($model->save()) {
                return redirect('/page')->with('success', "Page updated successfully");
            } else {
                return Redirect::back()->withInput()->with('error', 'Some error occured. Please try again later');
            }
    }

    public function destroy($id)
    {
        $model = Page::find($id);

        if(!$model){
            return returnNotFoundResponse('Page does not exist');
        }
        if($model->delete()){
            return returnSuccessResponse('Page deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }

    public function upload(Request $request)
{
    if($request->hasFile('upload')) {
        //get filename with extension
        $filenamewithextension = $request->file('upload')->getClientOriginalName();
   
        //get filename without extension
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
   
        //get file extension
        $extension = $request->file('upload')->getClientOriginalExtension();
   
        //filename to store
        $filenametostore = $filename.'_'.time().'.'.$extension;
   
        //Upload File
        $request->file('upload')->storeAs('public/uploads', $filenametostore);
 
        $CKEditorFuncNum = $request->input('CKEditorFuncNum');
        $url = asset('storage/uploads/'.$filenametostore); 
        $msg = 'Image successfully uploaded'; 
        $re = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
          
        // Render HTML output 
        @header('Content-type: text/html; charset=utf-8'); 
        echo $re;
    }
}
}
