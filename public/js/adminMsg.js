
$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
   
    $('body').on('click','.reply',function(){
        let id = $(this).data('id');
        addTextarea(id,'','');
        console.log(id);
    })
  
    $('body').on('click','.editAdminMsg',function(){
        let id = $(this).data('id');
        edit(id)
        console.log(id);
    })
    $('body').on('click','.delAdminMsg',function(){
        let id = $(this).data('id');
        $.ajax({
            type: "DELETE",
            url: "./msgBoard/del/" + id,
            success: function (data) {
                alert(data.success);
                window.location.reload();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });

    })
    $('body').on('click','.delUserMsg',function(){
        let id = $(this).data('id');
        $.ajax({
            type: "DELETE",
            url: "./msgBoard/delUserMsg/" + id,
            success: function (data) {
                alert(data.success);
                window.location.reload();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });

    })
    
    $('body').on('click','.saveMsg',function(){
        let id = $(this).data('id');
        let msg_id = '';
        if($(this).data('msg_id')){
            msg_id = $(this).data('msg_id');
        }
        reply(id,msg_id);
        console.log(id);
    })
    $('body').on('click','.cancel',function(){
        let id = $(this).data('id');
        cancel(id);
        console.log(id);
    })
    $('body').on('click','.cancel2',function(){
        let id = $(this).data('id');
        cancel2(id);
        console.log(id);
    })
})

function reply(id,msg_id) {
    let content = $("#replyForm textarea").val();
    if(content.length<=0){
        alert("請輸入訊息")
        return;
    }
    $.post('./msgBoard/reply',{'id':id,'msg_id':msg_id,'content':content},function(data){
        if(data.success){
            //alert(data.success);
            window.location.reload();
        }
    })
}
function edit(id){
    $.post('./msgBoard/edit/'+id,function(data){
        console.log(data);
        if(data){
            addTextarea(id,data.msgData_id,data.content)            
        }
    })
}
function cancel(id) {
    let btnHtml = `暫無回覆
        <a href="javascript:void(0)"   data-id="${id}"  class="float-md-right btn btn-primary btn-sm reply">回覆</a>
    `
    $("#showReplyMsg"+id).html(btnHtml);
}
function cancel2(id) {
    $.post('./msgBoard/edit/'+id,function(data){
        console.log(data);
        if(data){
            let btnHtml = 
            `內容:${data.content}
            <small  class="float-md-right form-text text-muted">${data.updated_at}</small>`
            $("#adminMsg"+id).html(btnHtml);
        }
    })
    
}

function addTextarea(id,msg_id,text) {
        
    if(text.length>0){
        let formHtml = `<form id="replyForm" class="">
        <div class="form-group">
            <label for="content">訊息</label>
            <textarea class="form-control" name="content" placeholder="">${text}</textarea>
        </div>
        
        <a href="javascript:void(0)" type="button" data-id="${id}" data-msg_id="${msg_id}" class="btn btn-primary saveMsg" >送出</a>
        <a href="javascript:void(0)" type="button" data-id="${id}" data-msg_id="${msg_id}" class="btn btn-danger cancel2" >取消</a>
    </form>`

    $("#adminMsg"+id).html(formHtml);
    }else{
        let formHtml = `<form id="replyForm" class="">
        <div class="form-group">
            <label for="content">訊息</label>
            <textarea class="form-control" name="content" placeholder="輸入訊息"></textarea>
        </div>
        
        <a href="javascript:void(0)" type="button" data-id="${id}" class="btn btn-primary saveMsg" >送出</a>
        <a href="javascript:void(0)" type="button" data-id="${id}" class="btn btn-danger cancel" >取消</a>
    </form>`

    $("#showReplyMsg"+id).html(formHtml);
    }
    
}