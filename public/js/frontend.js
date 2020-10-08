

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
$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#showBox").on('click','.this-product',function () {
        $('#shoppingCartModel').modal();
        $('#shopCartForm input[name=id]').val($(this).data('id'));
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
                $('#shoppingCartModel').modal('hide');
                $(obj).html('送出');
                $(form_name).trigger("reset");
                alert(data.success);
            },
            error: function (data) {
                $('#createNewCategory').html('送出');
                console.log('Error:', data);
            }
        });

    }
})