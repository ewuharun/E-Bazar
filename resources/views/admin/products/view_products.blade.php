@extends('layouts.adminLayout.admin_design')
@section('content')

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#">Products</a> <a href="#" class="current">View Products</a> </div>
    <h1>Products</h1>
    @if(Session::has('flash_message_error'))
            <div class="alert alert-error alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{!! session('flash_message_error') !!}</strong>
            </div>
        @endif   
        @if(Session::has('flash_message_success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{!! session('flash_message_success') !!}</strong>
            </div>
        @endif
  </div>
  <div class="container-fluid">
    <hr>
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
            <h5>Products</h5>
          </div>
          <div class="widget-content nopadding">
            <table class="table table-bordered data-table">
              <thead>
                <tr>
                  <th>Product ID</th>
                  <th>Category ID</th>
                  <th>Category Name</th>
                  <th>Product Name</th>
                
                  <th>Price</th>
                  <th>Unit</th>
                  <th>Image</th>
                  
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              	@foreach($products as $product)
                <tr class="gradeX">
                  <td class="center">{{ $product->id }}</td>
                  <td class="center">{{ $product->category_id }}</td>
                  <td class="center">{{ $product->category_name }}</td>
                  <td class="center">{{ $product->product_name }}</td>
                  
                  <td class="center"> {{ $product->price }} Tk</td>
                  <td class="center"> {{ $product->unit }} </td>
                  <td class="center">
                    @if(!empty($product->image1))
                    <img src="{{ asset('/images/backend_images/products/small/'.$product->image1) }}" style="width:90px;">
                    @endif
                  </td>

                  
                  <td class="center">
                    <a href="#myModal{{ $product->id }}" data-toggle="modal" class="btn btn-success btn-mini">View</a> 
                    <a href="{{ url('/admin/edit-product/'.$product->id) }}" class="btn btn-primary btn-mini">Edit</a> 
                    <a href="{{ url('/admin/add-attributes/'.$product->id) }}" class="btn btn-success btn-mini">Add</a>
                    <a href="{{ url('/admin/add-images/'.$product->id) }}" class="btn btn-info btn-mini">Add</a>
                    <a id="delProduct" rel="{{ $product->id }}" rel1="delete-product" href="{{ url('/admin/delete-product/'.$product->id) }}" class="btn btn-danger btn-mini deleteRecord">Delete</a>
 
                        <div id="myModal{{ $product->id }}" class="modal hide">
                          <div class="modal-header">
                            <button data-dismiss="modal" class="close" type="button">×</button>
                            <h3>{{ $product->product_name }} Full Details</h3>
                          </div>
                          <div class="modal-body">
                            <p>Product ID: {{ $product->id }}</p>
                            <p>Category ID: {{ $product->category_id }}</p>
                           
                            <p>Price: {{ $product->price }} TK</p>
                          
                            <p>Description: {{ $product->product_description }}</p>
                          </div>
                        </div>

                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection