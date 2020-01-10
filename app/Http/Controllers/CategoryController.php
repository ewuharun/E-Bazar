<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
    
    public function addCategory(Request $request){
        if($request->isMethod('post')){
            $data=$request->all();
            if(empty($data['availability'])){
                $status='0';
            }else{
                $status='1';
            }
            if(empty($data['description'])){
                $data['description'] = "";    
            }

            $category=new Category;
            $category->name=$data['category_name'];
            $category->description=$data['description'];
            $category->availability=$status;
            $category->save();
            return redirect()->back()->with('flash_message_success', 'Category has been added successfully');

        }


        return view('admin.categories.add_category');
    }

    public function viewCategories(){ 

        $categories = category::get();
        return view('admin.categories.view_categories')->with(compact('categories'));
    }

    public function editCategory(Request $request,$id=null){

        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); */

            if(empty($data['availability'])){
                $status='0';
            }else{
                $status='1';
            }
            
            if(empty($data['description'])){
                $data['description'] = "";    
            }
            
            Category::where(['id'=>$id])->update(['availability'=>$status,'name'=>$data['category_name'],'description'=>$data['description']]);
            return redirect()->back()->with('flash_message_success', 'Category has been updated successfully');
        }

        $categoryDetails = Category::where(['id'=>$id])->first();
        
        return view('admin.categories.edit_category')->with(compact('categoryDetails'));
    }

    public function deleteCategory($id = null){
        Category::where(['id'=>$id])->delete();
        return redirect()->back()->with('flash_message_success', 'Category has been deleted successfully');
    }



   
}
