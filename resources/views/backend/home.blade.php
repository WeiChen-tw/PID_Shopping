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
                        <a class="nav-link" id="v-pills-product-tab" data-toggle="pill" href="#v-pills-product" role="tab" aria-controls="v-pills-product" aria-selected="false">庫存管理</a>
                        <a class="nav-link" id="v-pills-category-tab" data-toggle="pill" href="#v-pills-category" role="tab" aria-controls="v-pills-category" aria-selected="false">商品分類管理</a>
                        <a class="nav-link" id="v-pills-return-tab" data-toggle="pill" href="#v-pills-return" role="tab" aria-controls="v-pills-return" aria-selected="false">退貨管理</a>
                        <a class="nav-link" id="v-pills-discount-tab" data-toggle="pill" href="#v-pills-discount" role="tab" aria-controls="v-pills-discount" aria-selected="false">設定優惠活動</a>
                        <a class="nav-link" id="v-pills-experience-tab" data-toggle="pill" href="#v-pills-experience" role="tab" aria-controls="v-pills-experience" aria-selected="false">等級機制</a>
                        <a class="nav-link" id="v-pills-myData-tab" data-toggle="pill" href="#v-pills-myData" role="tab" aria-controls="v-pills-myData" aria-selected="false">銷售數據</a>
                    </div>
                </div>
            </div>
            <div id="div-middle" class="col-md-9">
                <div class="row">
                    <div class="col">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-management" role="tabpanel" aria-labelledby="v-pills-management-tab">
                                <div class="container">
                                        <h1>會員管理</h1>
                                    <a class="btn btn-danger" href="javascript:void(0)" id="isBan" data-table="user">停權</a>
                                    <a class="btn btn-primary" href="javascript:void(0)" id="unBan" data-table="user">解除停權</a>

                                    <table id="usersTable" class="display" >
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" name="chkAll" data-table='#usersTable' /> </th>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Addr</th>
                                                <th>Phone</th>
                                                <th>Coin</th>
                                                <th>Banned</th>
                                                <th width="100px">Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot>

                                        </tfoot>
                                    </table>
                                </div>
                                <!-- User Modal -->
                                <div class="modal fade" id="ajaxUserModel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="userModelHeading"></h4>
                                            </div>
                                            <div class="modal-body">
                                                <form id="userForm" name="userForm" class="form-horizontal">
                                                    <input type="hidden" name="id" id="id">
                                                    <div class="form-group">
                                                        <label for="name" class="col-sm-2 control-label">Name</label>
                                                        <div class="col-sm-12">
                                                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="col-sm-2 control-label">Banned</label>
                                                        <div class="col-sm-12">
                                                            <select id="" name='banned' data-table="user">
                                                                <option value="N">N</option>
                                                                <option value="Y">Y</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="col-sm-2 control-label">Password</label>
                                                        <div class="col-sm-12">
                                                            <input type="text" class="form-control" id="password" name="password" placeholder="Enter Password" value="" maxlength="50" required="">
                                                        </div>
                                                    </div><div class="form-group">
                                                        <label for="name" class="col-sm-2 control-label">Email</label>
                                                        <div class="col-sm-12">
                                                            <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email" value="" maxlength="50" required="">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="col-sm-2 control-label">Addr</label>
                                                        <div class="col-sm-12">
                                                            <input type="text" class="form-control" id="addr" name="addr" placeholder="Enter Addr" value="" maxlength="50" required="">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="col-sm-2 control-label">Phone</label>
                                                        <div class="col-sm-12">
                                                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Phone" value="" maxlength="50" required="">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="col-sm-2 control-label">Coin</label>
                                                        <div class="col-sm-12">
                                                            <input type="text" class="form-control" id="coin" name="coin" placeholder="Enter Coin" value="" maxlength="50" required="">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-primary mb-2 saveBtn" href="javascript:void(0)" data-table="user" id=""> 儲存</a>
                                                <a class="btn btn-danger mb-2 closeModal" href="javascript:void(0)" id=""> 關閉</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END -->



                            </div>
                            
                            <div class="tab-pane fade" id ="v-pills-product" role="tabpanel" aria-labelledby="v-pills-product-tab">

                                <div class="container">
                                    <h1>Product CRUD</h1>
                                    <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Create New Product</a>
                                    <a class="btn btn-primary" href="javascript:void(0)" id="onMarket" data-table="products">上架</a>
                                    <a class="btn btn-danger" href="javascript:void(0)" id="takeOff" data-table="products">下架</a>
                                    <select id="sel"  data-table="products">
                                        <option value="">查詢分類</option>
                                        

                                    </select>
                                    <table id="myProducts" class="table table-bordered data-table">

                                        <thead>

                                            <tr>
                                                <th><input type="checkbox" name="chkAll" data-table='#myProducts'/> </th>
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
                                                                <input  name="category" id="category">
                                                                <select class ="selectpicker"    id="form-sel" data-table="products" multiple>
                                                                        
                                                                        
                                                                </select>
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

                                    <!-- Category Modal -->
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
                                    <!-- END -->
                                   
                                
                            </div>
                            <div class="tab-pane fade" id ="v-pills-return" role="tabpanel" aria-labelledby="v-pills-return-tab">
                                <div class="container">
                                    <h1>1</h1>
                                   
                                   
                                    
                                </div>
                            </div>
                            <div class="tab-pane fade" id ="v-pills-discount" role="tabpanel" aria-labelledby="v-pills-discount-tab">
                                <div class="container">
                                    <h2>設定優惠活動</h2>
                                    <form class="form-inline" id = "discountForm">
                                        <div class="form-group mx-sm-3 mb-2">
                                            <label for="method" class="">Method</label>
                                            <div class="">
                                                <select id="" name='method' class="form-control" data-table="discount">
                                                    <option value="1">滿額贈購物金</option>
                                                    <option value="2">滿額折扣%</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mx-sm-3 mb-2">
                                            <label for="inputTotal" class="">滿額</label>
                                            <input type="text" name="total" class="form-control" id="inputTotal" placeholder="$">
                                        </div>
                                        
                                        <div class="form-group mx-sm-3 mb-2">
                                            <label for="inputTotal" class="">優惠</label>
                                            <input type="text" name="discount" class="form-control" id="inputDiscount" placeholder="">
                                        </div>
                                        <a class="form-control btn btn-primary mb-2" href="javascript:void(0)" id="createNewDiscount"> 送出</a>

                                    </form>
                                    <table id="discountTable" class="table table-bordered " style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Method</th>
                                                <th>Total</th>
                                                <th>Discount</th>
                                                <th width="320px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>

                                        </tfoot>
                                    </table>
                                <!-- Discount Modal -->
                                <div class="modal fade" id="ajaxDiscountModel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="discountModelHeading"></h4>
                                            </div>
                                            <div class="modal-body">
                                                <form id="discountForm2" name="discountForm2" class="form-horizontal">
                                                    <input type="hidden" name="id" id="id">
                
                                                    <div class="form-group">
                                                        <label for="method" class="col-sm-2 control-label">Method</label>
                                                        <div class="col-sm-12">
                                                            <select id="" name='method' data-table="discount">
                                                                <option value="1">滿額贈購物金</option>
                                                                <option value="2">滿額折扣%</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="total" class="col-sm-2 control-label">金額</label>
                                                        <div class="col-sm-12">
                                                            <input type="text" class="form-control" id="" name="total" placeholder="Enter $" value="" maxlength="50" required="">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="discount" class="col-sm-2 control-label">優惠</label>
                                                        <div class="col-sm-12">
                                                            <input type="text" class="form-control" id="" name="discount" placeholder="" value="" maxlength="50" required="">
                                                        </div>
                                                    </div>
                                                
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-primary mb-2 saveBtn" href="javascript:void(0)" data-table="discount" id=""> 儲存</a>
                                                <a class="btn btn-danger mb-2 closeModal" href="javascript:void(0)" id=""> 關閉</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END -->
                                </div>
                            </div>
                            <div class="tab-pane fade" id ="v-pills-experience" role="tabpanel" aria-labelledby="v-pills-experience-tab">
                                <div class="container">
                                    <h1>3</h1>
                                   
                                   
                                    
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
            <!-- <div id="div-right" class="col-md-1">
                <div id="listDiv">

                </div>
            </div> -->
             <!-- ProductCategory Modal -->
             <div class="modal fade" id="ajaxProductListModel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="productListModelHeading"></h4>

                            <input type="hidden" name="id" >
                        </div>
                        <div class="modal-body">
                            <div id ="showBox" class="row pre-scrollable ">

                            </div>
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-primary mb-2" href="javascript:void(0)" id="saveProductBtn"> 送出</a>
                            <a class="btn btn-danger mb-2 closeModal" href="javascript:void(0)" id=""> 關閉</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END -->
        </div>
    </div>
    @push('scripts')
    <script src="{{ asset('js/product.js') }}"></script>
    <script src="{{ asset('js/table.js') }}"></script>
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script> -->
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    
    <link rel="stylesheet" href="{{ asset('css/my.css') }}">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css"> 

    <script>$.fn.selectpicker.Constructor.BootstrapVersion = '4';</script>
    @endpush
@endsection
