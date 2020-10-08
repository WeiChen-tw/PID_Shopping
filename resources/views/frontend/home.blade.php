@extends('layouts.app')

@section('content')
<div class="container">
    <div class="body row">
        <div class="col-md-2">
            <div class="col">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">個人資料</a>
                    <a class="nav-link active" id="v-pills-myShopCart-tab" data-toggle="pill" href="#v-pills-myShopCart" role="tab" aria-controls="v-pills-myShopCart" aria-selected="true">我的購物車</a>
                    <a class="nav-link " id="v-pills-checkOrder-tab" data-toggle="pill" href="#v-pills-checkOrder" role="tab" aria-controls="v-pills-checkOrder" aria-selected="true">查看訂單</a>
                    </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="col">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                            <div class="col">
                                <div class="card">
                                    <div class="card-header">個人資料</div>
                                    <div class="card-body">
                                    
                                        <form method="POST" action="{{url('/editProfile')}}" id="profileForm" name="profileForm" class="form-horizontal">
                                            {!! csrf_field() !!}
                                            <input type="hidden" name="id" id="id">
                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 control-label">Name</label>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="name" class="col-sm-4 control-label">Password</label>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control" id="password" name="password" placeholder="Enter Password" value="" maxlength="50" required="">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 col-form-label ">New Password</label>

                                                <div class="col-sm-12">
                                                    <input
                                                            type="password"
                                                            class="form-control{{ $errors->has('newPassword') ? ' is-invalid' : '' }}"
                                                            name="newPassword"
                                                            required
                                                    >
                                                    @if ($errors->has('newPassword'))
                                                        <div class="invalid-feedback">
                                                            <strong>{{ $errors->first('newPassword') }}</strong>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <label class="col-sm-4 col-form-label">Confirm New Password</label>

                                                <div class="col-sm-12">
                                                    <input
                                                            type="password"
                                                            class="form-control{{ $errors->has('newPassword_confirmation') ? ' is-invalid' : '' }}"
                                                            name="newPassword_confirmation"
                                                            required
                                                    >
                                                    @if ($errors->has('newPassword_confirmation'))
                                                        <div class="invalid-feedback">
                                                            <strong>{{ $errors->first('newPassword_confirmation') }}</strong>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group">
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
                                                    <input type="text" class="form-control" id="coin" name="coin" placeholder="Enter Coin" value="" maxlength="50" disabled>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-lg-6 offset-lg-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        Check
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        Reset
                                                    </button>
                                                </div>
                                            </div>
                                            
                                        </form>
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                        <div class="tab-pane fade show active" id="v-pills-myShopCart" role="tabpanel" aria-labelledby="v-pills-myShopCart-tab">
                            <form class="form-inline">
                                <div class="form-group ">
                                    <lable  class="" for="chkAll"> 全選 </lable><input class="form-control" type="checkbox" name="chkAll"  />
                                </div>
                                <div class="form-group">
                                    <button class="form-control btn-danger" onclick="delShopCartAll()">刪除</button> 
                                </div>
                                <div class="form-group">
                                    <button class="form-control btn-success" onclick="buy()">購買</button>
                                </div>
                            </form>
                            <div id="listDiv">

                            </div>
                        </div>
                        <div class="tab-pane fade" id="v-pills-checkOrder" role="tabpanel" aria-labelledby="v-pills-checkOrder-tab">
                            <div id="userOrder" class="row">


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
    <script src="{{ asset('js/user.js') }}"></script>
@endpush
@endsection
