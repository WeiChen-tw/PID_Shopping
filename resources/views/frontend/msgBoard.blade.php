@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" >
        <div class="col-md-2">
            
        </div>
        
        <div class="col-md-8">
            <h1>留言板</h1>
            
              <h2>{{$msgData->links()}}</h2>
            
            <div id="showMsgBox" style="height:60%;overflow:auto;">
                @foreach ($msgData as $row)
                    <div class=" col-md-12 " style="padding-top: 1.25rem;padding-bottom: 1.25rem;">
                        <div class="card border-primary " data-id="{{$row->id}}">
                            <h5 class="card-header bg-primary" style="color:white">
                                #{{$row->id}} 留言者:{{$row->email}}
                                @if($row->email==Auth::user()->email )
                                    <a href="javascript:void(0)"   data-id="{{$row->id}}"  class="float-md-right btn btn-warning btn-sm replyMsg">回覆</a>
                                    <a href="javascript:void(0)"   data-id="{{$row->id}}"  class="float-md-right btn btn-danger btn-sm delMsg">刪除</a>
                                @endif
                            </h5>
                            <div class="card-body">
                            <div id="showMsg{{$row->id}}">
                                @foreach($msgDataDetail as $msg)
                                    @if($msg->msgData_id == $row->id)
                                        @if($msg->auth=='user')
                                            <p id="userMsg{{$msg->id}}"class="card-text">
                                            內容:{{$msg->content}}
                                            <small  class="float-md-right form-text text-muted">{{$msg->updated_at}}</small>
                                            @if($row->email==Auth::user()->email )
                                                <a href="javascript:void(0)"   data-id="{{$msg->id}}"  class="float-md-right btn btn-success btn-sm editMsg">編輯</a>
                                            
                                            @endif
                                            </p>
                                        @else
                                            <h5 class="card-header">官方回覆</h5>
                                            <p id="adminMsg{{$msg->id}}" class="bg-light card-text">
                                            內容:{{$msg->content}}
                                            <small  class="float-md-right form-text text-muted">{{$msg->updated_at}}</small>
                                            </p>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                            </div>
                            <div id="showReplyMsg{{$row->id}}"class="card-footer ">
                                @foreach($reMsgData as $reMsg)
                                    @if($reMsg->id == $row->id)
                                        
                                        @break
                                    @endif
                                    @if($loop->last)
                                        暫無回覆
                                    
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div>
                <h2>{{$msgData->links()}}</h2>
            </div>
            <div class="col-md-12 border  " style="height:40%; padding-top: 1.25rem;position:static">
            <h3>使用者留言</h3>
                <form id="userMsgBoard" class="">
                    <div class="form-group ">
                        <label for="email">使用者身份</label>
                        <input type="email" class="form-control" name="email" value="{{ Auth::user()->name }} ({{ Auth::user()->email }})" placeholder="" readonly>
                        <small id="emailHelp" class="form-text text-muted">登入後才可留言</small>
                    </div>
                    <div class="form-group">
                        <label for="content">訊息</label>
                        <textarea class="form-control" name="content" maxlength="120" placeholder="輸入訊息"></textarea>
                        <small class="float-right form-text text-muted">字數限制120字元</small>
                    </div>
                    
                    <a type="button" class="btn btn-primary user-msg">送出</a>
                </form>
            </div>
        </div>
        <div class="col-md-2">
        </div>

    </div>
</div>
@push('scripts')
    <script src="{{ asset('js/userMsg.js') }}"></script>
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    
@endpush
@endsection
