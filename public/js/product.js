

$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let productTable;
    let categoryTable;
    let userTable;
    let discountTable;

    //會員管理分頁
   $('#v-pills-management-tab').on('click', function (e) {
    e.preventDefault()
    if (userTable) {
        userTable.destroy();
    }
    userTable = $('#usersTable').DataTable( {
        dom: "Bfrtip",
        "scrollY": "400px",
        "scrollX": true,
        "scrollCollapse": true,
        processing: true,
        serverSide: true,
        ajax: "./home/ajaxuser",
        columns: [
            { data: 'check', name: 'check', orderable: false, searchable: false },
            { data: "id" },
            { data: 'name', name: 'name' },
            { data: "email" },
            { data: "addr" },
            { data: "phone"}, 
            { data: "coin"}, 
            { data: "banned"}, 
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        select: true,
    } );
    
})
    //初始觸發會員管理分頁
    $('#v-pills-management-tab').trigger('click');
    //庫存管理分頁
    $('#v-pills-product-tab').on('click', function (e) {
        $.get("./home/ajaxcategory", function (data) {
            $("#sel").empty();
            $("#form-sel").empty();
            $("#sel").append('<option value="">查詢分類</option>')
            $.each(data.data, function (index, arr) {
                $("#sel").append('<option value="' + arr.name + '">' + arr.name + '</option>')
                $("#form-sel").append('<option value="' + arr.id + '">' + arr.name + '</option>')
                $("#form-sel").selectpicker('refresh');
            });
            
            
        })

        e.preventDefault()
        if (productTable) {
            productTable.destroy();
        }
        productTable = $('.data-table').DataTable({
            "scrollY": "400px",
            "scrollX": true,
            "scrollCollapse": true,
            //"paging": false,
            processing: true,
            serverSide: true,
            ajax: "./home/ajaxproducts",
            columns: [
                { data: 'check', name: 'check', orderable: false, searchable: false },
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'name', name: 'name' },
                { data: 'img', name: 'img', sortable: false, searchable: false },
                { data: 'onMarket', name: 'onMarket' },
                { data: 'category', name: 'category' },
                { data: 'price', name: 'price' },
                { data: 'quantity', name: 'quantity' },
                { data: 'quantitySold', name: 'quantitySold' },
                { data: 'description', name: 'description' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            select: true,
        
        });

    });

    //分類管理分頁
    $('#v-pills-category-tab').on('click', function (e) {
        e.preventDefault()
        if (categoryTable) {
            categoryTable.destroy();
        }
        categoryTable = $('#myCategory').DataTable({
            "scrollY": "400px",
            //"scrollX": true,
            "scrollCollapse": true,
            //"paging": false,
            processing: true,
            serverSide: true,
            ajax: "./home/ajaxcategory",
            columns: [
                { data: 'check', name: 'check', orderable: false, searchable: false },
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            select: true,

        });

    })

    //優惠活動分頁
    $('#v-pills-discount-tab').on('click', function (e) {
        e.preventDefault()
        if (discountTable) {
            discountTable.destroy();
        }
        discountTable = $('#discountTable').DataTable({
            // "scrollY": "400px",
            // "scrollX": true,
            // "scrollCollapse": true,
            //"paging": false,
            processing: true,
            serverSide: true,
            ajax: "./home/ajaxdiscount",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'null', render: function(data,type,row){
                    if(row.method=='1'){
                        return '滿額贈購物金';
                    }else if(row.method=='2'){
                        return '滿額折扣%';
                    }
                } },
                { data: 'total', name: 'total' },
                { data: 'discount', name: 'discount' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            select: true,

        });

    });

    
    //select category change product list
    $('#sel').on('change', function () {
        console.log($(this).data('table'));
        let table_name = $(this).data('table');
        let table;
        if (table_name == 'products') {
            table = productTable;
        }
        else if (table_name == 'category') {
            table = categoryTable;
        }
        table.columns(5).search(this.value).draw();
        $('input[name=chkAll').prop('checked',false);
    })
    //select all
    $('input[name=chkAll').change(function () {
        let isChecked = $(this).prop('checked');
        let table_name = $(this).data('table');
        console.log($(this).data('table'))
        $(table_name+' > tbody input:checkbox').prop('checked', isChecked);
    })

    $('#createNewProduct').click(function () {
        $('#saveBtn').val("create-product");
        $('#product_id').val('');
        $('#productForm').trigger("reset");
        $('#modelProductHeading').html("Create New Product");
        $('#ajaxProductModel').modal('show');
         //get select option data
        
    });

    function checkboxAction(table_name, action) {
        let table;
        let checkboxTable;
        let url;
        if (table_name == 'products'){
                table = productTable;
                checkboxTable = '#myProducts'
                url = "./onOrOff";
        }
        else if (table_name == 'user'){
                table = userTable;
                checkboxTable = '#usersTable'
                url = "./banOrUnban";
        }
        let checkboxes = $(checkboxTable+' > tbody input:checkbox')
        let arrCheckBox = [];
        $.each(checkboxes, function (index, status) {
            if (status['checked'] == true) {
                arrCheckBox.push($(status).data('id'));
            }
        })
        $.ajax({
            type: "POST",
            url: url,
            data: {
                'id': arrCheckBox,
                'action': action
            },
            success: function (data) {
                table.draw();
                $('input[name=chkAll').prop('checked',false);
                console.log(data) ;
            },
            error: function (data) {
                console.log('Error',data) ;
            }
        });
    }
    $('#onMarket').click(function () {
        checkboxAction($(this).data('table'), 'Y');
    });
    $('#takeOff').click(function () {
        checkboxAction($(this).data('table'), 'N');
    });
    $('#isBan').click(function () {
        checkboxAction($(this).data('table'), 'Y');
    });
    $('#unBan').click(function () {
        checkboxAction($(this).data('table'), 'N');
    });

    function setObjName(obj){
        switch (obj.table_name) {
            case 'products':
                obj.table = productTable;
                obj.form_name = '#productForm';
                obj.model_name = '#ajaxProductModel';
                break;
            case 'category':
                obj.table = categoryTable;
                obj.form_name = '#categoryForm2';
                obj.model_name = '#ajaxCategoryModel';
                break;
            case 'user':
                obj.table = userTable;
                obj.form_name = '#userForm';
                obj.model_name = '#ajaxUserModel';
                break;
            case 'discount':
                obj.table = discountTable;
                obj.form_name = '#discountForm2';
                obj.model_name = '#ajaxDiscountModel';
                break;
            default:
                break;
        }
    }
    $('body').on('click', '.edit', function () {
        let id = $(this).data("id");
        let obj = {
            table:null,
            table_name:$(this).data("table"),
            form_name:null,
            model_name:null
        };
        setObjName(obj);
        console.log($(this).data("table"), obj.model_name, $(this).data("id"))
        if (obj.table_name == 'products') {
            let product_id = $(this).data('id');
            $.get("./home/ajax" + obj.table_name + '/' + product_id + '/edit', function (data) {
                $('#productModelHeading').html("Edit Product");
                //$('#saveBtn').val("edit-user");
                $(obj.form_name + ' input[name=product_id]').val(data.productID);
                $(obj.form_name + ' input[name=name]').val(data.name);
                //$(obj.form_name + ' input[name=category]').val(data.category);
                $(obj.form_name + ' input[name=price]').val(data.price);
                $(obj.form_name + ' input[name=quantity]').val(data.quantity);
                $(obj.form_name + ' input[name=quantitySold]').val(data.quantitySold);
                $(obj.form_name + ' textarea[name=description]').val(data.description);
                $(obj.model_name).modal('show');
                $('#form-sel').selectpicker('val',['noneSelectedText'])
                $("#form-sel").selectpicker('refresh');
            })
        }
        else if (obj.table_name == 'category') {
            $.get("./home/ajax" + obj.table_name + '/' + id + '/edit', function (data) {
                $('#categoryModelHeading').html("Edit Category");
                //$('#saveBtn').val("edit-user");
                $(obj.model_name).modal('show');
                $(obj.form_name+' input[name=id]').val(data.id);
                $(obj.form_name+' input[name=name]').val(data.name);
                console.log(data);
            })
        }
        else if(obj.table_name == 'user'){
            $.get("./home/ajax" + obj.table_name + '/' + id + '/edit', function (data) {
                $('#userModelHeading').html("Edit User Info");
                //$('#saveBtn').val("edit-user");
                $(obj.model_name).modal('show');
                $(obj.form_name+' input[name=id]').val(data.id);
                $(obj.form_name+' input[name=name]').val(data.name);
                $(obj.form_name+' input[name=password]').val(data.password);
                $(obj.form_name+' input[name=email]').val(data.email);
                $(obj.form_name+' input[name=addr]').val(data.addr);
                $(obj.form_name+' input[name=phone]').val(data.phone);
                $(obj.form_name+' input[name=coin]').val(data.coin);
                $(obj.form_name+' select[name=banned]').val(data.banned);

                console.log(data);
            })
        }
        else if(obj.table_name == 'discount'){
            $.get("./home/ajax" + obj.table_name + '/' + id + '/edit', function (data) {
                $('#discountModelHeading').html("編輯優惠活動內容");
                $(obj.model_name).modal('show');
                $(obj.form_name+' input[name=id]').val(data.id);
                $(obj.form_name+' input[name=total]').val(data.total);
                $(obj.form_name+' input[name=discount]').val(data.discount);
                $(obj.form_name+' select[name=method]').val(data.method);

                console.log(data);
            })
        }
    });
    //save changes
    $('body').on('click', '.saveBtn', function (e) {
        e.preventDefault();
        let button = $(this);
        let obj = {
            table:null,
            table_name:$(this).data("table"),
            form_name:null,
            model_name:null
        };
             
        button.html('Sending..');
        setObjName(obj);
        console.log($(this).data("table"), $(this).data("id"))
        console.log($(obj.form_name).serialize());
        $.ajax({
            data: $(obj.form_name).serialize(),
            url: "./home/ajax" + obj.table_name,
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $(obj.form_name).trigger("reset");
                $(button).html('Save Changes');
                $(obj.model_name).modal('hide');
                obj.table.draw();
                $('input[name=chkAll').prop('checked',false);
                console.log(obj.table,'OK')
            },
            error: function (data) {
                console.log('Error:', data);
                $(button).html('Save Changes');
            }
        });
    });


    //delete
    $('body').on('click', '.delete', function () {
        let table;
        let table_name = $(this).data("table");
        let id = $(this).data("id");
        let yes;
        yes = confirm("Are you sure want to delete !");
        if(!yes){
            alert('You cancel delete. ');
            return;
        }
            
        if (table_name == 'products')
            table = productTable;
        else if (table_name == 'category')
            table = categoryTable;
        else if (table_name == 'discount')
            table = discountTable;

        $.ajax({
            type: "DELETE",
            //url: "{{ route('ajaxproducts.store') }}"+'/'+product_id,
            url: "./home/ajax" + table_name + '/' + id,
            success: function (data) {
                alert(data.success);
                table.draw();
                $('input[name=chkAll').prop('checked',false);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
    //set
    $('body').on('click', '.setProduct', function () {
        
        let model_name = ajaxProductListModel;
        let id = $(this).data("id");
        
        let dataTable = $(this).data("table");
        $("#showBox").empty();
        
       if(dataTable == 'category'){
            $("#productListModelHeading").html('新增分類商品');
            $("#saveProductBtn").attr('data-table', 'category');
       }else if(dataTable == 'discount'){
            $("#productListModelHeading").html('商品套用優惠活動');
            $("#saveProductBtn").attr('data-table', 'discount');
       }else{
           alert("Error");
           return;
       }
       $("#saveProductBtn").attr('data-id', id);
       $("#saveProductBtn").attr('data-action', 'add');
        $(model_name).modal('show');

        $.post("./getProductData", function (data) {
            $.each(data, function (index, arr) {
                let cardBody;
                if (arr.img != '') {
                    cardBody = `<img class="img-fluid" src="data:image/jpeg;base64,` + arr.img + `" ></img>`;
                } else {
                    cardBody = `<h3>No Image</h3>`
                }
                $("#showBox").append(` <div class="product col-md-4 " style="padding-bottom: 1.25rem;">
                        <div class="product-list card bg-default" data-product_id="`+ arr.productID + `">
                            <h5 class="card-header">`
                    + arr.name +
                    `</h5>
                            <div class="card-body">`
                    + cardBody +
                    `<p class="card-text">`
                    + arr.description +
                    `</p>
                            </div>
                            <div class="card-footer ">
                                Card footer
                            </div>
                        </div>
                    </div>`
                );
            })
        })

    });

    $('body').on('click', '.removeProduct', function () {

        let model_name = ajaxProductListModel;
        let id = $(this).data("id");
        $("#showBox").empty();
        $("#saveProductBtn").attr('data-id', id);
        $("#saveProductBtn").attr('data-action', 'remove');
        $(model_name).modal('show');
        let dataTable = $(this).data("table");
        if(dataTable == 'category'){
            $("#productListModelHeading").html('移除分類商品');
            $("#saveProductBtn").attr('data-table', 'category');
       }else if(dataTable == 'discount'){
            $("#productListModelHeading").html('移除商品套用優惠活動');
            $("#saveProductBtn").attr('data-table', 'discount');
       }else{
           alert("Error");
           return;
       }
        $.post("./getProductData", { 'table':dataTable,'id': id }, function (data) {
            if (data.length == 0) {
                $("#showBox").append(`<h3>查無商品</h3`);
            }
            
            $.each(data, function (index, arr) {
                let cardBody;
                if (arr.img != '') {
                    cardBody = `<img class="img-fluid" src="data:image/jpeg;base64,` + arr.img + `" ></img>`;
                } else {
                    cardBody = `<h3>No Image</h3>`
                }
                $("#showBox").append(` <div class="product col-md-4 " style="padding-bottom: 1.25rem;">
                        <div class="product-list card bg-default" data-product_id="`+ arr.productID + `">
                            <h5 class="card-header">`
                    + arr.name +
                    `</h5>
                            <div class="card-body">`
                    + cardBody +
                    `<p class="card-text">`
                    + arr.description +
                    `</p>
                            </div>
                            <div class="card-footer ">
                                Card footer
                            </div>
                        </div>
                    </div>`
                );
            })
        })
    });

    let selectCardArr = [];
    $('body').off('click', '.product-list').on('click', '.product-list', function () {

        $(this).toggleClass('border-danger');
        selectCardArr.push($(this).data('product_id'));
        console.log($(this), selectCardArr);
    })

    $("#saveProductBtn").click(function () {
        let result = new Set(selectCardArr);
        result = [...result];
        let id = document.querySelector("#saveProductBtn").getAttribute('data-id')
        let table = document.querySelector("#saveProductBtn").getAttribute('data-table')
        let action = document.querySelector("#saveProductBtn").getAttribute('data-action')

        let url ;
        if(table =='category'){
            url = './setProductCategory'
        }else if(table=='discount'){
            url = './setProductDiscount'
        }else{
            alert("Error");
            return;
        }
        selectCardArr = [];
        $('.product-list').removeClass('border-danger');
        $.ajax({
            data: {
                id: id,
                action:action,
                product_id_arr: result
            },
            url: url,
            type: "POST",
            dataType: 'json',
            success: function (data) {
                if(action =='remove'){
                    $("#showBox").empty();
                    $.post("./getProductData", { 'table':table,'id': id }, function (data) {
                        if (data.length == 0) {
                            $("#showBox").append(`<h3>查無商品</h3`);
                        }
                        
                        $.each(data, function (index, arr) {
                            let cardBody;
                            if (arr.img != '') {
                                cardBody = `<img class="img-fluid" src="data:image/jpeg;base64,` + arr.img + `" ></img>`;
                            } else {
                                cardBody = `<h3>No Image</h3>`
                            }
                            $("#showBox").append(` <div class="product col-md-4 " style="padding-bottom: 1.25rem;">
                                    <div class="product-list card bg-default" data-product_id="`+ arr.productID + `">
                                        <h5 class="card-header">`
                                + arr.name +
                                `</h5>
                                        <div class="card-body">`
                                + cardBody +
                                `<p class="card-text">`
                                + arr.description +
                                `</p>
                                        </div>
                                        <div class="card-footer ">
                                            Card footer
                                        </div>
                                    </div>
                                </div>`
                            );
                        })
                    })
                    
                }
                alert(data.success);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });

        
    });

    
    $(".closeModal").on('click', function () {
        $("#ajaxProductListModel").modal('hide')
        console.log($(this))
    })
    //---------------------- Create -------------------//
    $('#createNewCategory').click(function (e) {
        e.preventDefault();
        $(this).html('Sending..');
        console.log($('#categoryForm').serialize());
        if($("#inputCaretory").val().length!=0){
            $.ajax({
                data: $('#categoryForm').serialize(),
                url: "./home/ajaxcategory",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#createNewCategory').html('送出');
                    $('#categoryForm').trigger("reset");
                    categoryTable.draw();
                    $('input[name=chkAll').prop('checked',false);
                    alert(data.success);
                },
                error: function (data) {
                    $('#createNewCategory').html('送出');
                    console.log('Error:', data);
    
                }
            });
        }else{
            $('#createNewCategory').html('送出');
            alert('輸入錯誤');
        }
        
    });

    $('#createNewDiscount').click(function (e) {
        let formName = '#discountForm';
        e.preventDefault();
        $(this).html('Sending..');
        console.log($(formName).serialize());
        let isTotal = $("#inputTotal").val().length!=0;
        let isDiscount = $("#inputDiscount").val().length!=0;
        if(isTotal && isDiscount){
            $.ajax({
                data: $(formName).serialize(),
                url: "./home/ajaxdiscount",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#createNewDiscount').html('送出');
                    $(formName).trigger("reset");
                    discountTable.draw();
                    $('input[name=chkAll').prop('checked',false);
                    alert(data.success);
                },
                error: function (data) {
                    $('#createNewDiscount').html('送出');
                    console.log('Error:', data);
    
                }
            });
        }else{
            $('#createNewDiscount').html('送出');
            alert('輸入錯誤');
        }
        
    });

    $('.selectpicker').change(function () {
        $('#category').val( $('.selectpicker').val());
        //alert(selectedItem);
    });
})



    //   $.ajaxSetup({

    //       headers: {

    //           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

    //       }

    // });



