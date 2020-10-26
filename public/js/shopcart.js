
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
            $(form_name+' input[name=level]').val(data.level);
            $(form_name+' input[name=exp_bar]').val(data.exp_bar);
            $(form_name+' input[name=coin]').val(data.coin);
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
        let locationURL = window.document.location.origin;
        orderTable = $('#orderTable').DataTable( {
            // dom: "Bfrtip",
            // "scrollY": "400px",
            // "scrollX": true,
            // "scrollCollapse": true,
            language:{
                url: locationURL+'/public/Chinese-traditional.json',
                decimal:',',
                thousands:'.'
            },
            order:[0,"desc"],
            processing: true,
            serverSide: true,
            ajax: "./getOrder",
            columns: [
                { data: "id" },
                { data: 'created_at' },
                { data: 'status' },
                { data: 'addr'},
                { data: 'null', render: function(data,type,row){
                    if(row.sysMethod==null){
                        return '無';
                    }else if(row.sysMethod=='1'){
                        return '滿'+row.sysTotal+'贈$'+row.sysDiscount+'購物金,得$'+row.orderDiscount;
                    }else if(row.sysMethod=='2'){
                        return '滿'+row.sysTotal+'折扣$'+row.sysDiscount+'%,折扣'+row.orderDiscount;
                    }
                } },
                { data:null, render:function(data,type,row){
                    if(row.sysMethod == '2'){
                        return '$'+(row.total-row.orderDiscount);
                    }else{
                        return '$'+row.total;
                    }
                }},
                { data: 'use_coin'},
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
     $('#v-pills-myShopCart-tab').trigger('click');
    $('#profileResetBtn').on('click',function(){
        initProfile();
    })
    //initShopCart();
    function showList(list) {
        
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
            let id = list[i]['productID'];
            let name = list[i]['name'];
            let price = list[i]['price'];
            let quantity = list[i]['quantity'];;
            let discount = list[i]['discount'];
            if(discount==null){
                discount='無';
            }
            let src = "data:image/jpeg;base64," + (list[i]['img']); 
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
            <label class="" for="checkbox${id}">
        選取                            
        </label>
        <input name="checkBuy" type="checkbox" id=checkbox${id} value ="${id}" style="zoom:180%;margin-left:0.25rem;margin-top:0.25rem;"class="">                            
        
        `);
            $('#' + idName + ' #list-body-info').append(`
        <label>商品編號:</label><span class="productID">${id}</span><br>
        <label>商品名稱:</label><span class="datatime">${name}</span><br>
        <label>價格:</label><span id="price${id}" value ="${price}" class="price">${price*quantity}</span><br>
        <label>優惠活動名稱 #</label><span id="discount${id}" value ="${discount}" class="discount">${discount}</span><br>
        <label>數量:&nbsp;</label><button id="minus" name = "${id}"value="${quantity}"onclick="minus(this)">-</button>
        <input class="w-25 changePrice"   name="${id}"id="inputQuantity${id}" oninput="value=value.replace(/[^\\d]/g,'')" type="text" value="${quantity}"  >
        <button id="plus" name = "${id}"value="${quantity}"onclick="plus(this)">+</button><br>
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
        //console.log("plus",value,$(obj.name));
        
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
   
    
    $('#v-pills-tabContent').on('change','.changePrice',function(){
        changePrice($(this));
    })
    delShopCart = function(obj) {
        let id = obj.value;
        console.log(id)
        let yes;
        yes = confirm("確定要刪除嗎?");
        if(!yes){
            alert('你取消了刪除動作.');
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
     let other_sum;
     let amount;
     let coin;
     buy = function(obj) {
        let chk_id = []; //定義一個產品編號陣列
        let chk_quantity = [];
        let price = [];
        let idx =[];
        $('input[name="checkBuy"]:checked').each(function(key) { //遍歷每一個名字為checkBuy的核取方塊，其中選中的執行函式  
            chk_id.push($(this).val()); //將選中的值新增到陣列chk_value中  
            chk_quantity.push($("#inputQuantity" + chk_id[chk_id.length - 1]).val());
            idx.push(key);
            price.push($("#price" +chk_id[chk_id.length-1]).attr('value')*chk_quantity[key]);
            //console.log(chk_quantity);
        });
        $("#form-sel").empty();
        $("#checkoutForm div[name=result]").empty();
        if (chk_id.length > 0) {
            $.ajax({
                type: "POST",
                url: "./getOrderDiscount",
                data:{
                    quantity: chk_quantity,
                    productID: chk_id,
                },
                success: function(data) {
                    
                    if(data.success){
                        //alert(data.success);
                        //delShopCartAll();
                        $("#form-sel").append('<option value="0">不使用優惠活動</option>')
                        $("#checkoutForm div[name=result]").html('<h3>總結帳金額$:'+data.amount[0]+'</h3><h4>可使用購物金餘額$'+data.coin+'</h4>');
                        $("#checkoutForm input[name=addr]").val(data.addr);
                        $.each(data.total, function (index, arr) {
                            if(index==0){
                                return true;
                            }
                            $("#form-sel").append('<option value="' + index + '">' + arr+data.discount[index]  +'</option>')
                        });
                        $("#checkoutForm input[name=discount]").val(0);
                        other_sum = data.other;
                        amount = data.amount;
                        coin = data.coin;
                        console.log(data);
                    }else if(data.wrong){
                        alert(data.wrong);
                        console.log('wrong');
                        initShopCart();
                    }else if(data.error){
                        $("#form-sel").append('<option value="0">' + data.error + '</option>')
                        $("#checkoutForm input[name=discount]").val('0');
                        $("#checkoutForm input[name=addr]").val(data.addr);
                        $("#checkoutForm div[name=result]").html('<h3>總結帳金額$:'+data.amount+'</h3><h4>可使用購物金餘額$'+data.coin+'</h4>');
                    }
                    
                },
                error: function(data){
                    console.log('Error:',data);
                }
            })
        }else{
            alert('請選擇商品');
            return;
        }
        $("#ajaxCheckoutModel").modal('show');
        // $('#list-body-info .price').each(function(key) { 
        //     price.push($(this).text())
        // });
        $("#checkDetail").empty();
        $('#list-body-info .datatime').each(function(key) { 
            if(key == idx[key]){
                $("#checkDetail").append(`
                    <p>商品名稱:${$(this).text()} 數量${chk_quantity[key]} 金額:$${price[key]} </p>
                `);
            }
            
        })
        
    }
    $("#form-sel").on('change',function(){
        $id = $(this).val();
        console.log('sel',$(this).val(),other_sum);
        $("#checkoutForm div[name=result]").empty();
        if($id=='0'){
            $("#checkoutForm div[name=result]").html('<h3>總結帳金額$:'+amount[$id]+'</h3><h4>可使用購物金餘額$'+coin+'</h4>');
        }else{
            $("#checkoutForm div[name=result]").html('<p>'+other_sum[$id]+'</p><h3>總結帳金額$:'+amount[$id]+'</h3><h4>可使用購物金餘額$'+coin+'</h4>');
            $("#checkoutForm input[name=discount]").val($id);
        }
    })
    $(".buy").on('click',function(){
        let addr = $("#checkoutForm input[name=addr]").val();
        let coin = $("#checkoutForm input[name=coin]").val();
        let discount_id = $("#checkoutForm input[name=discount]").val();
        let sel_id = $("#checkoutForm select option:selected").val();
        let chk_id = []; //定義一個產品編號陣列
        let chk_quantity = [];
        if(addr.length<=0){
            alert('請輸入地址');
            return;
        }
        
        $('input[name="checkBuy"]:checked').each(function() { //遍歷每一個名字為checkBuy的核取方塊，其中選中的執行函式  
            chk_id.push($(this).val()); //將選中的值新增到陣列chk_value中  
            chk_quantity.push($("#inputQuantity" + chk_id[chk_id.length - 1]).val());
            //console.log(chk_quantity);
        });
        console.log(chk_id);
        if (chk_id.length > 0) {
            $.ajax({
                type: "POST",
                url: "./home/ajaxorderdetail",
                data:{
                    sel_id:sel_id,
                    discount_id:discount_id,
                    addr: addr,
                    quantity: chk_quantity,
                    productID: chk_id,
                    coin:coin,
                },
                success: function(data) {
                    
                    if(data.success){
                        $("#ajaxCheckoutModel").modal('hide');
                        $("#checkDetail").empty();
                        alert(data.success);
                        delShopCartAll();
                        console.log('success');
                    }else{
                        $("#ajaxCheckoutModel").modal('hide');
                        $("#checkDetail").empty();
                        $('#v-pills-myShopCart-tab').trigger('click');
                        alert(data.wrong);
                        
                        console.log('wrong');
                    }
                    
                },
                error: function(data){
                    console.log('Error:',data);
                }
            })
        }
    })
    $('body').on('click','.取消訂單',function(){
        let id = $(this).data('id');
        yes = confirm("確定要取消訂單 ？");
        if(!yes){
            alert('你取消了操作');
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
    $('body').on('click','.取貨',function(){
        let id = $(this).data('id');
        yes = confirm("確定取貨 ？");
        if(!yes){
            alert('你取消了操作');
            return;
        }
        $.ajax({
            data:{
                id:id
            },
            type: "POST",
            url: "./receipt" ,
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
    $('body').on('click','.退貨',function(){
        let id = $(this).data('id');
        yes = confirm("確定退貨 ？");
        if(!yes){
            alert('你取消了操作');
            return;
        }
        $.ajax({
            data:{
                id:id
            },
            type: "POST",
            url: "./returnOrder" ,
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
    $('body').on('click','.商品退貨',function(){
        let id = $(this).data('id');
        let product_id = $(this).data('product_id');
        yes = confirm("確定退貨該商品 ？");
        if(!yes){
            alert('你取消了操作');
            return;
        }
        $.ajax({
            data:{
                id:id,
                product_id:product_id
            },
            type: "POST",
            url: "./returnOrderDetail" ,
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
            <td>編號 : ${orderDetail[index].productID}</td>
            <td>商品名稱 : ${orderDetail[index].name}</td>
            <td>${orderDetail[index].quantity} 件</td>
            <td>$${orderDetail[index].total}</td>
            <td>${orderDetail[index].status}</td>
        `;
        if (orderDetail[index].status =='已付款取貨'){
            htmlText+=`
                <td> 
                    <a href="javascript:void(0)" 
                        data-toggle="tooltip"  
                        data-table="order" 
                        data-id="${orderDetail[index].id}" 
                        data-product_id="${orderDetail[index].productID}"
                        data-original-title="" 
                        class="btn btn-danger btn-sm 商品退貨">商品退貨
                    </a>
                </td>
            </tr>
            `;
        }else{
            htmlText+='</tr>'
        }
    }
    htmlText+= '</table>';
    return htmlText;
}
function changePrice(obj) {
    price = $("#price" + obj.name).attr("value");
    $("#price" + obj.name).text(price * obj.value);
    console.log(obj.value);
}