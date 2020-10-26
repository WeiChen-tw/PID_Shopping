@extends('layouts.app')

@section('content')
<div class="container">
    <div class="body row">
        <div class="col-md-2">
            <div class="col-sm-12">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" id="v-pills-myShopCart-tab" data-toggle="pill" href="#v-pills-myShopCart" role="tab" aria-controls="v-pills-myShopCart" aria-selected="true">我的購物車</a>
                    <a class="nav-link " id="v-pills-checkOrder-tab" data-toggle="pill" href="#v-pills-checkOrder" role="tab" aria-controls="v-pills-checkOrder" aria-selected="true">查看訂單</a>
                    </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="col">
                    <div class="tab-content" id="v-pills-tabContent">
                        
                        <div class="tab-pane fade show active" id="v-pills-myShopCart" role="tabpanel" aria-labelledby="v-pills-myShopCart-tab">
                            <form class="form-inline">
                                <div class="form-group ">
                                    <lable  class="" for="chkAll"> 全選 </lable><input class="form-control" type="checkbox" style="zoom:180%" name="chkAll"  />
                                
                                </div>
                                <div class="form-group">
                                    <a class="form-control btn btn-danger" href="javascript:void(0)" onclick="delShopCartAll()">刪除</a> 
                                </div>
                                <div class="form-group">
                                    <a class="form-control btn btn-success" href="javascript:void(0)"  onclick="buy()">購買</a>
                                </div>
                            </form>
                            <div id="listDiv">

                            </div>
                            <!-- Checkout Modal -->
                            <div class="modal fade" id="ajaxCheckoutModel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="checkoutyModelHeading">確認訂單明細</h4>
                                        </div>
                                        <div class="modal-body">
                                            <form id="checkoutForm" name="checkoutForm" class="form-horizontal">
                                                <input type="hidden" name="id" id="id">
                                                <div class="form-group">
                                                    <label for="" class="col-sm-6 control-label"></label>
                                                    <div id = "checkDetail"class="col-sm-12 ">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="" class="col-sm-4 control-label">優惠活動</label>
                                                    
                                                    <input type="hidden" class="form-control" id="" name="discount"  value="" maxlength="50" required="">
                                                    <input type="hidden" class="form-control" id="" name="otherSum"  value="" maxlength="50" required="">
                                                    <input type="hidden" class="form-control" id="" name="amount"  value="" maxlength="50" required="">
                                                    
                                                    <div class="col-sm-12">
                                                        <select id="form-sel" name='discount' class="form-control" data-table="">
                                                        
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-12"name="result">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="coin" class="col-sm-2 control-label">購物金</label>
                                                    <div class="col-sm-12">
                                                        <input type="text" class="form-control" id="" name="coin" placeholder="輸入購物金" value="" maxlength="50" required="">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="addr" class="col-sm-2 control-label">地址</label>
                                                    <div class="col-sm-12">
                                                        <input type="text" class="form-control" id="" name="addr" placeholder="輸入地址" value="" maxlength="50" required="">
                                                    </div>
                                                </div>
                                                <div class="col-sm-offset-2 col-sm-10">
                                                    <a class="btn btn-primary mb-2 buy" data-table="" href="javascript:void(0)" > 購買</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END -->
                        </div>
                        <div class="tab-pane fade" id="v-pills-checkOrder" role="tabpanel" aria-labelledby="v-pills-checkOrder-tab">
                            <div id="userOrder" class="row">
                                <h1>訂單查詢</h1>
                                <table id="orderTable" class="table table-bordered " style="width:100%"> 
                                    <thead>
                                        <tr>
                                            <th>訂單編號</th>
                                            <th>時間</th>
                                            <th>狀態</th>
                                            <th>地址</th>
                                            <th>優惠</th>
                                            <th>總金額</th>
                                            <th>使用購物金</th>
                                            <th width="80px">明細</th>
                                            <th width="120px">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>

                                    </tfoot>
                                </table>
                                <table id="orderDetailTable" class="table table-bordered " style="width:100%"> 
                                    <thead>
                                        <tr>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>

                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-1">
        </div>

    </div>
</div>
@push('scripts')
    <script src="{{ asset('js/shopcart.js') }}"></script>
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    
@endpush
@endsection
