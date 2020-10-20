
function init(params) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:"GET",
        url:"./load_product",
        dataType:"json",
        success:function(data){
            $("#showBox").html(data.html)
            console.log(data.success)
        },
        error:function(xhr){
            alert(xhr.status)
        }
    })
}
init();
$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    initSelect();
    $("#form-sel").on('change',function(){
        let category_id = $(this).val();
        console.log('id',category_id);
        if(category_id){
            $.post('./searchCategory',{'category_id':category_id},function(data){
                $("#showBox").html(data.html);
            })
        }else{
            init();
        }
        
    })
    $("#showBox").on('click','.this-product',function () {
        let product_id = $(this).data('id');
        $('#shoppingCartModel').modal();
        $('#shopCartForm input[name=id]').val(product_id);
        $.get("./getProduct"+'/' + product_id , function (data) {
            //$('#shopCartForm label[name=product_id]').val(data.productID);
            $('#shopCartForm label[name=name]').text('品名:'+data.name);
            $('#shopCartForm label[name=price]').text('價格:'+data.price);
            $('#shopCartForm label[name=sys_quantity]').text('庫存量:'+data.quantity);
            $('#shopCartForm label[name=quantitySold]').text('售出量:'+data.quantitySold);
            $('#shopCartForm textarea[name=description]').val(data.description);
        });
    })
    $('body').on('click','.saveBtn',function(){
        clickCard($(this))
    })
    function clickCard(obj) {
       
        $(obj).html('Sending..');
        console.log("clickCard", $(obj).attr("value"));
        let id = $(obj).data("id");
        let form_name = '#shopCartForm';
        console.log($(form_name).serialize());
        $.ajax({
            data: $(form_name).serialize(),
            url: "./home/ajaxshopcart",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                if(data.success){
                    $('#shoppingCartModel').modal('hide');
                    $(obj).html('送出');
                    $(form_name).trigger("reset");
                    alert(data.success);
                }
                if(data.login){
                    document.location.href="http://www.shopping.net/public/login"
                }
               
            },
            error: function (data) {
                $('#createNewCategory').html('送出');
                console.log('Error:', data);
            }
        });

    }
    $("#searchKeyword").on('click',function(){
        let keyword = $("#searchForm input[name=keyword]").val();
        if(keyword.length<0){
            alert('請輸入關鍵字');
            return;
        }
        $.post('./searchKeyword',{'keyword':keyword},function(data){
            $("#showBox").html(data.html);
        })
    });
})

function initSelect() {
    $.get("./getCategory", function (data) {
        $("#form-sel").empty();
        $("#form-sel").append('<option value="">查詢分類</option>');
        $.each(data.success, function (index, arr) {
            $("#form-sel").append('<option value="' + arr.id + '">' + arr.name + '</option>');
        });
    })
}
