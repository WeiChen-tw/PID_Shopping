
$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(".user-msg").on('click',function(){
        let content = $("#userMsgBoard textarea[name=content]").val()
        console.log(content);
        $.post('./msgBoard/sendMsg',{'content':content},function(data){
            if(data.success){
                alert(data.success);
            }
        });
        $("#userMsgBoard textarea[name=content]").val('');
        window.location.reload();
    });
  
    $('body').on('click','.editMsg',function(){
        let id = $(this).data('id');
        edit(id)
        console.log(id);
    })
    $('body').on('click','.delMsg',function(){
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
    
    $('body').on('click','.saveMsg',function(){
        let id = $(this).data('id');
        send(id);
        console.log(id);
    })
    $('body').on('click','.cancel',function(){
        let id = $(this).data('id');
        cancel(id);
        console.log(id);
    })
})

function send(id) {
    let content = $("#editForm textarea").val();
    if(content.length<=0){
        alert("請輸入訊息")
        return;
    }
    $.post('./msgBoard/sendMsg',{'id':id,'content':content},function(data){
        if(data.success){
            alert(data.success);
            window.location.reload();
        }
    })
}
function edit(id){
    $.post('./msgBoard/edit/'+id,function(data){
        console.log(data);
        if(data){
            addTextarea(id,data.content)            
        }
    })
}
function cancel(id) {
    $.post('./msgBoard/edit/'+id,function(data){
        console.log(data);
        if(data){
            $("#showUserMsg"+id).html(data.content);       
        }
    })
    
}
function addTextarea(id,text) {
        
    if(text.length>0){
        let formHtml = `<form id="editForm" class="">
        <div class="form-group">
            <label for="content">訊息</label>
            <textarea class="form-control" name="content" placeholder="">${text}</textarea>
        </div>
        
        <a href="javascript:void(0)" type="button" data-id="${id}" class="btn btn-primary saveMsg" >送出</a>
        <a href="javascript:void(0)" type="button" data-id="${id}" class="btn btn-danger cancel" >取消</a>
    </form>`

    $("#showUserMsg"+id).html(formHtml);
    }else{
        let formHtml = `<form id="editForm" class="">
        <div class="form-group">
            <label for="content">訊息</label>
            <textarea class="form-control" name="content" placeholder="輸入訊息"></textarea>
        </div>
        
        <a href="javascript:void(0)" type="button" data-id="${id}" class="btn btn-primary saveMsg" >送出</a>
        <a href="javascript:void(0)" type="button" data-id="${id}" class="btn btn-danger cancel" >取消</a>
    </form>`

    $("#showUserMsg"+id).html(formHtml);
    }
    
}