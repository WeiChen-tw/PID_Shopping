@extends('layouts.app')

@section('content')
<!-- <div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div> -->

<div class="container-fluid">
        <div class="row">
            <div class="otherHeader col-md-12">
                
            </div>
            
        </div>
        <div class="body row">
            <div class="col-md-2">
                <div class="col">
                    <div class="nav flex-column nav-pills " id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="v-pills-management-tab" data-toggle="pill" href="#v-pills-management" role="tab" aria-controls="v-pills-management" aria-selected="true">會員管理</a>
                        <a class="nav-link" id="v-pills-commodity-tab" data-toggle="pill" href="#v-pills-commodity" role="tab" aria-controls="v-pills-commodity" aria-selected="false">我的商品</a>
                        <a class="nav-link" id="v-pills-product-tab" data-toggle="pill" href="#v-pills-product" role="tab" aria-controls="v-pills-product" aria-selected="false">庫存管理</a>
                        <a class="nav-link" id="v-pills-category-tab" data-toggle="pill" href="#v-pills-category" role="tab" aria-controls="v-pills-category" aria-selected="false">商品分類管理</a>
                        <a class="nav-link" id="v-pills-product-tab" data-toggle="pill" href="#v-pills-" role="tab" aria-controls="v-pills-" aria-selected="false">退貨管理</a>
                        <a class="nav-link" id="v-pills-product-tab" data-toggle="pill" href="#v-pills-" role="tab" aria-controls="v-pills-" aria-selected="false">設定優惠活動</a>
                        <a class="nav-link" id="v-pills-product-tab" data-toggle="pill" href="#v-pills-" role="tab" aria-controls="v-pills-" aria-selected="false">等級機制</a>
                        <a class="nav-link" id="v-pills-myData-tab" data-toggle="pill" href="#v-pills-myData" role="tab" aria-controls="v-pills-myData" aria-selected="false">銷售數據</a>
                    </div>
                </div>
            </div>
            <div id="div-middle" class="col-md-9">
                <div class="row">
                    <div class="col">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-management" role="tabpanel" aria-labelledby="v-pills-management-tab">
                                <form>
                                    <div id="usersManagement" class="row">


                                    </div>
                                    <div id="formFooter">
                                        <button id="managementButton" class="btn btn-success" type="button" onclick="management()">確認操作</button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="v-pills-commodity" role="tabpanel" aria-labelledby="v-pills-commodity-tab">

                                <!-- autocomplete='off' -->
                                <form id="putForm" enctype="multipart/form-data" method="post" action="/PID_Assignment/core/Upload.php" onsubmit="return false">
                                    <div class="form-group row">
                                        <label for="name" class="col-2 col-form-label">商品名稱</label>
                                        <div class="col-10">
                                            <input id="name" name="name" type="text" class="form-control" required="required">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="category" class="col-2 col-form-label">類別</label>
                                        <div class="col-10">
                                            <select id="category" name="category" class="custom-select" aria-describedby="categoryHelpBlock" required="required">
                                                <option value="1">本季主打</option>
                                                <option value="2">經典火車</option>
                                            </select>
                                            <span id="categoryHelpBlock" class="form-text text-muted">請選擇分類</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="quantity" class="col-2 col-form-label">數量</label>
                                        <div class="col-10">
                                            <input id="quantity" name="quantity" placeholder="1" type="text" required="required" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="price" class="col-2 col-form-label">價格</label>
                                        <div class="col-10">
                                            <input id="price" name="price" placeholder="$" type="text" class="form-control" required="required">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="description" class="col-2 col-form-label">商品描述</label>
                                        <div class="col-10">
                                            <textarea id="description" name="description" cols="40" rows="5" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label" for="Shipping">運費</label>
                                        <div class="col-10">
                                            <input id="Shipping" name="Shipping" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleFormControlFile1">預覽商品圖片</label>
                                        <div id="previewDiv"></div>
                                        <div class="offset-4 col-10">
                                            <input id="uploadImage" type="file" name="image" class="custom-file-input">
                                            <label class="custom-file-label" for="image" style="width:200px">Choose file...</label>
                                        </div>

                                    </div>
                                    <div class="form-group row">
                                        <div class="offset-4 col-10">
                                            <button id="uploadButton" type="button" class="btn btn-primary" onclick="upload()">確定上架</button>
                                            <button id="cancelButton" type="button" class="btn btn-primary" onclick="cancel()">取消</button>
                                        </div>
                                    </div>
                                </form>
                                <button id="showButton" type="button" class="btn btn-success" value="">已上架商品</button>

                            </div>
                            <div class="tab-pane fade" id ="v-pills-product" role="tabpanel" aria-labelledby="v-pills-product-tab">
                            
                                <div class="container">
                                    <h1>Product CRUD</h1>
                                    <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Create New Product</a>
                                    <a class="btn btn-primary" href="javascript:void(0)" id="onMarket" data-table="products">上架</a>
                                    <a class="btn btn-danger" href="javascript:void(0)" id="takeOff" data-table="products">下架</a>
                                    <select id="sel" data-table="products">
                                        <option value="">查詢分類</option>
                                        
                                    </select>
                                    <table id="myProducts" class="table table-bordered data-table">

                                        <thead>

                                            <tr>
                                                <th><input type="checkbox" name="chkAll" /> </th>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Img</th>
                                                <th>onMarket</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>QuantitySold</th>
                                                <th>Descriptions</th>
                                                <th width="100px">Action</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th><input type="checkbox" name="chkAll" /> </th>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Img</th>
                                                <th>onMarket</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>QuantitySold</th>
                                                <th>Descriptions</th>
                                                <th width="280px">Action</th>
                                            </tr>

                                        </tfoot>
                                    </table>
                                </div>
                                    <div class="modal fade" id="ajaxProductModel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="productModelHeading"></h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="productForm" name="productForm" class="form-horizontal">
                                                        <input type="hidden" name="product_id" id="product_id">
                                                        <div class="form-group">
                                                            <label for="name" class="col-sm-2 control-label">Name</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">
                                                            </div>
                                                        </div>

                                                        <!-- <div class="form-group">
                                                            <label for="img" class="col-sm-2 control-label">Img</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="img" name="img" placeholder="Enter Img" value="" maxlength="50" required="">
                                                            </div>
                                                        </div> -->

                                                        <div class="form-group">
                                                            <label for="category" class="col-sm-2 control-label">Category</label>
                                                            <div class="col-sm-12">
                                                            <select class ="selectpicker" multiple   id="form-sel" data-table="products" >
                                                                
                                                            </select>
                                                            <!-- <input type="hidden" name="category" id="category"> -->
                                                                <!-- <input type="text" class="form-control" id="category" name="category" placeholder="Enter Category" value="" maxlength="50" required=""> -->
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="price" class="col-sm-2 control-label">Price</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="price" name="price" placeholder="Enter Price" value="" maxlength="50" required="">
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="quantity" class="col-sm-2 control-label">Quantity</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Enter Quantity" value="" maxlength="50" required="">
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="quantitySold" class="col-sm-2 control-label">QuantitySold</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="quantitySold" name="quantitySold" placeholder="Enter QuantitySold" value="" maxlength="50" required="">
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="col-sm-2 control-label">Descriptions</label>
                                                            <div class="col-sm-12">
                                                                <textarea id="description" name="description" required="" placeholder="Enter Descriptions" class="form-control"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-offset-2 col-sm-10">
                                                            <a class="btn btn-primary mb-2 saveBtn" data-table="products" href="javascript:void(0)" > Save Changes</a>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                               
                            </div>
                            <div class="tab-pane fade" id ="v-pills-category" role="tabpanel" aria-labelledby="v-pills-category-tab">
                            
                                <div class="container">
                                    <h1>Categories CRUD</h1>
                                    <form class="form-inline" id = "categoryForm">
                                        <div class="form-group mb-2">
                                            <label for="staticCaretoryText" class="sr-only">新增類別</label>
                                            <input type="text"  readonly class="form-control-plaintext" id="staticCaretoryText" value="新增類別">
                                        </div>
                                        <div class="form-group mx-sm-3 mb-2">
                                            <label for="inputCaretory" class="sr-only"></label>
                                            <input type="text" name="name" class="form-control" id="inputCaretory" placeholder="名稱">
                                        </div>
                                        <a class="btn btn-primary mb-2" href="javascript:void(0)" id="createNewCategory"> 送出</a>
                                        
                                    </form>
                                    <!-- <select id="sel">
                                        <option value="">查詢分類</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                    </select> -->
                                    <table id="myCategory" class="table table-bordered ">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" name="chkAll" /> </th>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th width="320px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                        
                                        </tfoot>
                                    </table>
                                    </div>
                                    <div class="modal fade" id="ajaxCategoryModel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="categoryModelHeading"></h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="categoryForm2" name="categoryForm" class="form-horizontal">
                                                        <input type="hidden" name="id" id="id">
                                                        <div class="form-group">
                                                            <label for="name" class="col-sm-2 control-label">Name</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-offset-2 col-sm-10">
                                                            <a class="btn btn-primary mb-2 saveBtn" data-table="category" href="javascript:void(0)" > Save Changes</a>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="ajaxProductCategoryModel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="ProductCategoryModelHeading"></h4>
                                                    
                                                    <input type="hidden" name="id" >
                                                </div>
                                                <div class="modal-body">
                                                    <div id ="showBox" class="row pre-scrollable ">
                                                       
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <a class="btn btn-primary mb-2" href="javascript:void(0)" id="setProductCategory"> 送出</a>
                                                    <a class="btn btn-danger mb-2 closeModal" href="javascript:void(0)" id=""> 關閉</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="v-pills-myData" role="tabpanel" aria-labelledby="v-pills-myData-tab">
                                <div class="row">
                                    <h2><label id="label-lineChart">報表類別：最近7天銷售額</label></h2>
                                </div>
                                <div class="row">&nbsp;</div>
                                <div class="row">
                                    <button id="btn-last7days" class="btn btn-primary">最近7天銷售額</button> &nbsp;
                                    <button id="btn-last30days" class="btn btn-success">最近30天銷售額</button>
                                </div>

                                <div id="my_dataviz"></div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
            <div id="div-right" class="col-md-1">
                <div id="listDiv">

                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script src="{{ asset('js/product.js') }}"></script>
     <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="{{ asset('css/my.css') }}">
@endpush
@endsection
