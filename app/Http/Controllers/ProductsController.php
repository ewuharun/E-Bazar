<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Session;
use Image;
use App\Category;
use App\Product;
use App\ProductsAttribute;
use App\ProductsImage;
use App\Coupon;
use App\User;
use App\Country;
use App\DeliveryAddress;
use App\Order;
use App\OrdersProduct;
use DB;

class ProductsController extends Controller
{
    public function addProduct(Request $request){

        if($request->isMethod('post')){
			$data = $request->all();
			//echo "<pre>"; print_r($data); die;

            $product = new Product;
            $product->product_name = $data['product_name'];
			$product->category_id = $data['category_id'];
			
			
            if(!empty($data['weight'])){
                $product->unit = $data['weight'];
            }else{
                $product->weight = 0; 
            }
			if(!empty($data['description'])){
				$product->product_description = $data['description'];
			}else{
				$product->product_description = '';	
			}
          
            if(empty($data['status'])){
                $status='0';
            }else{
                $status='1';
            }
            
			$product->price = $data['price'];

			// Upload Image
            if($request->hasFile('image')){
            	$image_tmp = $request->image;
                //$fileName = time() . '.'.$image_tmp->clientExtension();
                if ($image_tmp->isValid()) {
                    // Upload Images after Resize
                    $extension = $image_tmp->getClientOriginalExtension();
	                $fileName = rand(111,99999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large'.'/'.$fileName;
                    $medium_image_path = 'images/backend_images/products/medium'.'/'.$fileName;  
                    $small_image_path = 'images/backend_images/products/small'.'/'.$fileName;  

	                Image::make($image_tmp)->save($large_image_path);
 					Image::make($image_tmp)->resize(600, 600)->save($medium_image_path);
     				Image::make($image_tmp)->resize(300, 300)->save($small_image_path);

     				$product->image1 = $fileName; 

                }
            }

            // Upload Video
            /*if($request->hasFile('video')){
                $video_tmp = Input::file('video');
                $video_name = $video_tmp->getClientOriginalName();
                $video_path = 'videos/';
                $video_tmp->move($video_path,$video_name);
                $product->video = $video_name;
            }*/

            
            $product->availability = $status;
			$product->save();
			return redirect()->back()->with('flash_message_success', 'Product has been added successfully');
		}

		$categories = Category::all();

		$categories_drop_down = "<option value='' selected disabled>Select</option>";
		foreach($categories as $cat){
			$categories_drop_down .= "<option value='".$cat->id."'>".$cat->name."</option>";
			
		}

		//echo "<pre>"; print_r($categories_drop_down); die;

        

		return view('admin.products.add_product')->with(compact('categories_drop_down'));
    }


    //view products
    public function viewProducts(Request $request){
		$products = Product::get();
		foreach($products as $key => $val){
			$category_name = Category::where(['id' => $val->category_id])->first();
			$products[$key]->category_name = $category_name->name;
		}
		$products = json_decode(json_encode($products));
		//echo "<pre>"; print_r($products); die;
		return view('admin.products.view_products')->with(compact('products'));
    }
    
    public function deleteProduct($id = null){
        Product::where(['id'=>$id])->delete();
        return redirect()->back()->with('flash_message_success', 'Product has been deleted successfully');
    }



    public function editProduct(Request $request,$id=null){

		if($request->isMethod('post')){
			$data = $request->all();
			/*echo "<pre>"; print_r($data); die;*/

            if(empty($data['status'])){
                $status='0';
            }else{
                $status='1';
            }


			// Upload Image
            if($request->hasFile('image')){
            	$image_tmp = $request->image;
                if ($image_tmp->isValid()) {
                    // Upload Images after Resize
                    $extension = $image_tmp->getClientOriginalExtension();
	                $fileName = rand(111,99999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large'.'/'.$fileName;
                    $medium_image_path = 'images/backend_images/products/medium'.'/'.$fileName;  
                    $small_image_path = 'images/backend_images/products/small'.'/'.$fileName;  

	                Image::make($image_tmp)->save($large_image_path);
 					Image::make($image_tmp)->resize(600, 600)->save($medium_image_path);
     				Image::make($image_tmp)->resize(300, 300)->save($small_image_path);

                }
            }else if(!empty($data['current_image'])){
            	$fileName = $data['current_image'];
            }else{
            	$fileName = '';
            }

            // Upload Video
            /*if($request->hasFile('video')){
                $video_tmp = Input::file('video');
                $video_name = $video_tmp->getClientOriginalName();
                $video_path = 'videos/';
                $video_tmp->move($video_path,$video_name);
                $videoName = $video_name;
            }else if(!empty($data['current_video'])){
                $videoName = $data['current_video'];
            }else{
                $videoName = '';
            }*/

            if(empty($data['description'])){
            	$data['description'] = '';
            }

			Product::where(['id'=>$id])->update(['availability'=>$status,'category_id'=>$data['category_id'],'product_name'=>$data['product_name'],
				'product_description'=>$data['description'],'price'=>$data['price'],'unit'=>$data['weight'],'image1'=>$fileName]);
		
			return redirect()->back()->with('flash_message_success', 'Product has been edited successfully');
		}

		// Get Product Details start //
		$productDetails = Product::where(['id'=>$id])->first();
		// Get Product Details End //

		// Categories drop down start //
		$categories = Category::all();

		$categories_drop_down = "<option value='' disabled>Select</option>";
		foreach($categories as $cat){
			if($cat->id==$productDetails->category_id){
				$selected = "selected";
			}else{
				$selected = "";
			}
			$categories_drop_down .= "<option value='".$cat->id."' ".$selected.">".$cat->name."</option>";
			
			
		}
		// Categories drop down end //

       

		return view('admin.products.edit_product')->with(compact('productDetails','categories_drop_down'));
    } 
    
    public function deleteProductImage($id){

		// Get Product Image
		$productImage = Product::where('id',$id)->first();

		// Get Product Image Paths
		$large_image_path = 'images/backend_images/products/large/';
		$medium_image_path = 'images/backend_images/products/medium/';
		$small_image_path = 'images/backend_images/products/small/';

		// Delete Large Image if not exists in Folder
        if(file_exists($large_image_path.$productImage->image1)){
            unlink($large_image_path.$productImage->image1);
        }

        // Delete Medium Image if not exists in Folder
        if(file_exists($medium_image_path.$productImage->image1)){
            unlink($medium_image_path.$productImage->image1);
        }

        // Delete Small Image if not exists in Folder
        if(file_exists($small_image_path.$productImage->image1)){
            unlink($small_image_path.$productImage->image1);
        }

        // Delete Image from Products table
        Product::where(['id'=>$id])->update(['image1'=>'']);

        return redirect()->back()->with('flash_message_success', 'Product image has been deleted successfully');
    }
    

   
    
}
