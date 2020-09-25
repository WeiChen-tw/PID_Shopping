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
                        <a class="nav-link" id="v-pills-product-tab" data-toggle="pill" href="#v-pills-product" role="tab" aria-controls="v-pills-product" aria-selected="false">商品管理</a>
                        <a class="nav-link" id="v-pills-myData-tab" data-toggle="pill" href="#v-pills-myData" role="tab" aria-controls="v-pills-myData" aria-selected="false">銷售數據</a>
                    </div>
                </div>
            </div>
            <div id="div-middle" class="col-md-6">
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
                                    <h1>Laravel 5.8 Ajax CRUD tutorial using Datatable - ItSolutionStuff.com</h1>
                                    <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Create New Product</a>

                                    <table class="table table-bordered data-table">

                                        <thead>

                                            <tr>

                                                <th>No</th>

                                                <th>Name</th>

                                                <th>Details</th>

                                                <th width="280px">Action</th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                        </tbody>

                                    </table>
                                </div>
                                <div class="modal fade" id="ajaxModel" aria-hidden="true">

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <div class="modal-header">

                                                <h4 class="modal-title" id="modelHeading"></h4>

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



                                                    <div class="form-group">

                                                        <label class="col-sm-2 control-label">Details</label>

                                                        <div class="col-sm-12">

                                                            <textarea id="detail" name="detail" required="" placeholder="Enter Details" class="form-control"></textarea>

                                                        </div>

                                                    </div>



                                                    <div class="col-sm-offset-2 col-sm-10">

                                                    <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes

                                                    </button>

                                                    </div>

                                                </form>

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
            <div id="div-right" class="col-md-4">
                <div id="listDiv">

                </div>
            </div>
        </div>
    </div>
   
@endsection
