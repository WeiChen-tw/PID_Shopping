
$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let orderTable;
    let orderDetailTable;
    function initShopCart(){
        $.ajax({
            url: "./home/ajaxshopcart",
            type: "GET",
            dataType: 'json',
            success: function (data) {
                console.log(data);
                showList(data);
                //alert(data.success);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    
    }
    function initProfile(){
        $.get("./home/ajaxuser", function (data) {
            let form_name = "#profileForm"
            $(form_name+' input[name=id]').val(data.id);
            $(form_name+' input[name=name]').val(data.name);
            $(form_name+' input[name=password]').val(data.password);
            $(form_name+' input[name=email]').val(data.email);
            $(form_name+' input[name=addr]').val(data.addr);
            $(form_name+' input[name=phone]').val(data.phone);
            $(form_name+' input[name=coin]').val(data.coin);
            $(form_name+' select[name=banned]').val(data.banned);
         });
    }
    $('#v-pills-myShopCart-tab').on('click', function (e) {
        initShopCart();
    });
    $('#v-pills-profile-tab').on('click', function (e) {
        initProfile();
    })
    $('#v-pills-checkOrder-tab').on('click', function (e) {
        
        e.preventDefault()
        if (orderTable) {
            orderTable.destroy();
        }
        orderTable = $('#orderTable').DataTable( {
            // dom: "Bfrtip",
            // "scrollY": "400px",
            // "scrollX": true,
            // "scrollCollapse": true,
            language:{
                decimal:',',
                thousands:'.'
            },
            processing: true,
            serverSide: true,
            ajax: "./getOrder",
            columns: [
                { data: "id" },
                { data: 'created_at' },
                { data: 'status' },
                { data: 'total' ,
                    name:'total',
                    render: function(data,type,full,meta){
                        return '$'+data;
                    }},
                { data: 'details', name: 'details', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            select: true,
        } );
        
    })
    $('#orderTable > tbody').on('click','.details-control',function(){
        let tr = $(this).closest('tr');
        let id = $(this).data('id');
        let row = orderTable.row(tr);
        if(row.child.isShown()){
            row.child.hide();
            tr.removeClass('shown');
        }else{
            row.child( format(row.data(),id) ).show();
            tr.addClass('shown');
            console.log(id)
        }
    })
    $('#v-pills-profile-tab').trigger('click');
    $('#profileResetBtn').on('click',function(){
        initProfile();
    })
    //initShopCart();
    function showList(list) {
        console.log(list);
        $("#listDiv").empty();
        $("#listDiv").append(`                        
            <div id="listDivRow0" class="shadow-sm p-3 mb-5 bg-white rounded row" style="margin:1%">                        
                <div id="list-check" class="col-md-2 ">
                
                </div>
            <div id="list-header" class="col-md-3 ">
                <img class="img-fluid" src="">
            </div>
            <div id="list-body" class="col-md-5">
                <div id="list-body-info" >
                </div>
            </div>
                <div id="list-footer" class="col-md-2">
                    
                </div>
            </div>`)
        count = list.length;
        for (let i = 0; i < count; i++) {
            id = list[i]['productID'];
            name = list[i]['name'];
            price = list[i]['price'];
            quantity = list[i]['quantity'];;
            sellerID = list[i]['sellerID'];
            src = "data:image/jpeg;base64," + (list[i]['img']); 
            let idName = "listDivRow"
            idx = i;
            nextIdx = i + 1;
            idName += (idx.toString());
            if (i < count - 1) {
                var addDiv = $("#listDivRow" + idx).clone(true).attr("id", "listDivRow" + nextIdx)
                $("#" + idName).after(addDiv);
            }
            $('#' + idName + ' #list-header img').attr("src", src);
            $('#' + idName + ' #list-check').append(`
        <input name="checkBuy" type="checkbox" value ="${id}" style="width:30%;height:30%;margin-left:2.25rem;margin-top:2.25rem;"class="form-check-input">                            
        <label class="form-check-label"style="margin-left:2.55rem" for="checkbox">
        選取                            
        </label>
        `);
            $('#' + idName + ' #list-body-info').append(`
        <label>商品編號:</label><span class="productID">${id}</span><br>
        <label>商品名稱:</label><span class="datatime">${name}</span><br>
        <label>價格:</label><span id="price${id}" value ="${price}" class="price">${price*quantity}</span><br>
        <label>數量:&nbsp;</label><button id="minus" name = "${id}"value="${quantity}"onclick="minus(this)">-</button><input class="w-25" name="${id}"id="inputQuantity${id}" type="text" value="${quantity}" onkeydown="" onkeyup="changePrice(this),value=value.replace(/[^\\d]/g,'')"><button id="plus" name = "${id}"value="${quantity}"onclick="plus(this)">+</button><br>
        `);
            $('#' + idName + ' #list-footer').append(`
        <button class="form-control btn-link" onclick="delShopCart(this)" value="${id}">刪除</button>`)
            // if (i == count - 1) {
            //     $('#' + idName + ' #list-body-info').append(`<br><button onclick="buy(this)">直接購買</button>`);
            // }
        }
    }

    plus = function(obj) {                   
        let $inputQuantity = obj.name;
        let value = $("#inputQuantity" + obj.name).val();
        $("#inputQuantity" + obj.name).val(++value);
        price = $("#price" + obj.name).attr("value");
        $("#price" + obj.name).text(price * value);
        console.log("plus",value,$(obj.name));
        
    }
    minus = function(obj) {
        
        let $inputQuantity = obj.name;
        let value = $("#inputQuantity" + obj.name).val();
        if(value>0){
            $("#inputQuantity" + obj.name).val(--value);
        }                    
        price = $("#price" + obj.name).attr("value");
        $("#price" + obj.name).text(price * value);
        //console.log("plus",value,$(obj.name));
    }
    changePrice = function(obj){
        price = $("#price" + obj.name).attr("value");
        $("#price" + obj.name).text(price * obj.value);
        console.log(obj.value);
    }
    delShopCart = function(obj) {
        let id = obj.value;
        console.log(id)
        let yes;
        yes = confirm("Are you sure want to delete !");
        if(!yes){
            alert('You cancel delete. ');
            return;
        }
        $.ajax({
            type: "DELETE",
            url: "./home/ajaxshopcart"+'/' + id,
            success: function (data) {
                initShopCart();
                alert(data.success);
                
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
   
     }
     $('input[name=chkAll').change(function () {
        let isChecked = $(this).prop('checked');
        $('input[name="checkBuy"]').prop('checked', isChecked);
    })
     delShopCartAll = function (){
        let chk_id = []; //定義一個產品編號陣列
        $('input[name="checkBuy"]:checked').each(function() { //遍歷每一個名字為checkBuy的核取方塊，其中選中的執行函式  
            chk_id.push($(this).val()); //將選中的值新增到陣列chk_id中  
        });
        let idArr= chk_id;
        $.ajax({
            type: "DELETE",
            url: "./home/ajaxshopcart"+'/' + idArr,
            success: function (data) {
                initShopCart();
                alert(data.success);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
   
     }
     buy = function(obj) {
        var chk_id = []; //定義一個產品編號陣列
        var chk_quantity = [];
        $('input[name="checkBuy"]:checked').each(function() { //遍歷每一個名字為checkBuy的核取方塊，其中選中的執行函式  
            chk_id.push($(this).val()); //將選中的值新增到陣列chk_value中  
            chk_quantity.push($("#inputQuantity" + chk_id[chk_id.length - 1]).val());
            console.log(chk_quantity);
        });
        console.log(chk_id);
        if (chk_id.length > 0) {
            $.ajax({
                type: "POST",
                url: "./home/ajaxorderdetail",
                data:{
                    quantity: chk_quantity,
                    productID: chk_id,
                },
                success: function(data) {
                    
                    if(data.success){
                        alert(data.success);
                        delShopCartAll();
                        console.log('success');
                    }else{
                        alert(data.wrong);
                        console.log('wrong');
                    }
                    
                },
                error: function(data){
                    console.log('Error:',data);
                }
            })
        }
    }
    $('body').on('click','.cancelOrder',function(){
        let id = $(this).data('id');
        yes = confirm("Are you sure want to cancel order !");
        if(!yes){
            alert('You canceled the action. ');
            return;
        }
        $.ajax({
            data:{
                id:id
            },
            type: "DELETE",
            url: "./cancelOrder" ,
            success: function (data) {
                alert(data.success);
                orderTable.draw();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
        console.log(id);
    })
})

function format ( d ,id) {
    // `d` is the original data object for the row
    let orderDetail;
    let htmlText ='<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    $.ajax({
        async:false,
        data: {
            id: id,
        },
        url: './getOrderDetail',
        type: "POST",
        dataType: 'json',
        success: function (data) {
           
            orderDetail = data;
            console.log(data,orderDetail);
        },
        error:function (data){

        }
    });
    console.log(orderDetail)
    for (let index = 0; index < orderDetail.length; index++) {
        let name = 'drink';
        htmlText += `
        <tr>
            <td>name : ${orderDetail[index].name}</td>
            <td>${orderDetail[index].quantity} 件</td>
            <td>$${orderDetail[index].total}</td>
            
        </tr>`
    }
    htmlText+= '</table>';
    return htmlText;
}