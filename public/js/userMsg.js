$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(".user-msg").on('click', function () {
        let content = $("#userMsgBoard textarea[name=content]").val()
        console.log(content);
        if (content.length <= 0) {
            alert("請輸入訊息")
            return;
        }
        $.post('./msgBoard/sendMsg', {
            'content': content
        }, function (data) {
            if (data.success) {
                alert(data.success);
            }
        });
        $("#userMsgBoard textarea[name=content]").val('');
        window.location.reload();
    });

    $("body").on('click','.replyMsg',function(){
        let id = $(this).data('id');
        //let msgData_id = $(this).data('msgdata_id');
        getMsg(id);
    });
    $('body').on('click', '.editMsg', function () {
        let id = $(this).data('id');
        edit(id)
        console.log(id);
    })
    $('body').on('click', '.delMsg', function () {
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

    $('body').on('click', '.saveMsg', function () {
        let id = $(this).data('id');
        let msg_id = $(this).data('msg_id');
        send(id,msg_id);
        console.log(id);
    })
    $('body').on('click', '.saveReplyMsg', function () {
        let id = $(this).data('id');
        let msg_id = $(this).data('msg_id');
        reply(id,msg_id);
        console.log(id);
    })
    $('body').on('click', '.cancel', function () {
        let id = $(this).data('id');
        cancel(id);
        console.log(id);
    })
    $('body').on('click', '.cancelReply', function () {
        let id = $(this).data('id');
        let msg_id = $(this).data('msg_id');
        cancelReply(id);
        console.log(id);
    })
})

function send(id,msg_id) {
    let content = $("#editForm textarea").val();
    if (content.length <= 0) {
        alert("請輸入訊息")
        return;
    }
    $.post('./msgBoard/editMsg', {
        'id': id,
        'msg_id':msg_id,
        'content': content
    }, function (data) {
        if (data.success) {
            alert(data.success);
            window.location.reload();
        }
    })
}
function reply(id,msg_id) {
    let content = $("#editForm textarea").val();
    if (content.length <= 0) {
        alert("請輸入訊息")
        return;
    }
    $.post('./msgBoard/reply', {
        'id': id,
        'msg_id':msg_id,
        'content': content
    }, function (data) {
        if (data.success) {
            alert(data.success);
            window.location.reload();
        }
    })
}
function edit(id) {
    $.post('./msgBoard/edit/' + id, function (data) {
        console.log(data);
        if (data) {
            addTextarea(data.id,data.msgData_id, data.content, 'edit')
        }
    })
}

function getMsg(id) {
    $.post('./msgBoard/getMsg/' + id, function (data) {
        console.log(data);
        if (data) {
            let htmlText =`<div id="showMsg${data.msgData_id}">`;
            data.forEach((row,index) => {
                if(row.auth=='user'){
                    htmlText+= ` <p class="card-text">
                    內容:${row.content}
                    <small  class="float-md-right form-text text-muted">${row.updated_at}</small>
                    </p>`
                }else{
                    htmlText+= ` <p class="bg-light card-text">
                    內容:${row.content}
                    <small  class="float-md-right form-text text-muted">${row.updated_at}</small>
                    </p>`
                }
            });
            htmlText+='</div>';
            addTextarea(id,data[0].id, htmlText, 'reply')
        }
    })
}

function cancel(id) {
    $.post('./msgBoard/edit/' + id, function (data) {
        console.log(data);
        if (data) {
            let htmlText = ` <p class="card-text">
            內容:${data.content}
            <small  class="float-md-right form-text text-muted">${data.updated_at}</small>
            <a href="javascript:void(0)"   data-id="${id}"  class="float-md-right btn btn-success btn-sm editMsg">編輯</a>
            </p>`;
            $("#userMsg" + id).html(htmlText);
        }
    })
}

function cancelReply(id) {
    $.post('./msgBoard/getMsg/' + id, function (data) {
        console.log(data);
        
        if (data) {
            let htmlText =`<div id="showMsg${data.msgData_id}">`;
            data.forEach((row,index) => {
                if(row.auth='user'){
                    htmlText+= ` <p class="card-text">
                    內容:${row.content}
                    <small  class="float-md-right form-text text-muted">${row.updated_at}</small>
                    <a href="javascript:void(0)"   data-id="${row.id}"  class="float-md-right btn btn-success btn-sm editMsg">編輯</a>
                    </p>`
                }else{
                    htmlText+= ` <p class="bg-light card-text">
                    內容:${row.content}
                    <small  class="float-md-right form-text text-muted">${row.updated_at}</small>
                    </p>`
                }
            });
            htmlText+='</div>';
            $("#showMsg" + id).html(htmlText);
        }
    })
}

function addTextarea(id,msg_id, text, action) {

    if (text.length > 0) {
        let formHtml = null;
        if (action == 'reply') {
            formHtml = `<form id="editForm" class="">
            <div class="form-group">
                ${text}
                <textarea class="form-control" name="content" placeholder=""></textarea>
            </div>
            
            <a href="javascript:void(0)" type="button" data-id="${id}" data-msg_id="${msg_id}" class="btn btn-primary saveReplyMsg" >送出</a>
            <a href="javascript:void(0)" type="button" data-id="${id}" data-msg_id="${msg_id}" class="btn btn-danger cancelReply" >取消</a>
            </form>`
            $("#showMsg" + id).html(formHtml);
        } else if (action == 'edit') {
            formHtml = `<form id="editForm" class="">
            <div class="form-group">
                <label for="content">訊息</label>
                <textarea class="form-control" name="content" placeholder="">${text}</textarea>
            </div>
            
            <a href="javascript:void(0)" type="button" data-id="${id}" data-msg_id="${msg_id}" class="btn btn-primary saveMsg" >送出</a>
            <a href="javascript:void(0)" type="button" data-id="${id}" data-msg_id="${msg_id}" class="btn btn-danger cancel" >取消</a>
            </form>`
            $("#userMsg" + id).html(formHtml);
        }


        
    } else {
        let formHtml = `<form id="editForm" class="">
        <div class="form-group">
            <label for="content">訊息</label>
            <textarea class="form-control" name="content" placeholder="輸入訊息"></textarea>
        </div>
        
        <a href="javascript:void(0)" type="button" data-id="${id}" class="btn btn-primary saveMsg" >送出</a>
        <a href="javascript:void(0)" type="button" data-id="${id}" class="btn btn-danger cancel" >取消</a>
    </form>`

        $("#showMsg" + id).html(formHtml);
    }

}