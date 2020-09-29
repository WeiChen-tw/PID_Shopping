
function test(){
    console.log('test');
}
$(document).ready(function(){

       $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var productTable ;
    $('#v-pills-product-tab').on('click', function(e) {
        e.preventDefault()
        if(productTable){
            productTable.destroy();
        }
        productTable = $('.data-table').DataTable({
            "scrollY": "400px",
            "scrollX": true,
            "scrollCollapse": true,
            "paging":         false,
            processing: true,
            serverSide: true,
            ajax: "./home/ajaxproducts",
            columns: [
                {data: 'check', name: 'check', orderable: false, searchable: false},
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'name', name: 'name'},
                {data:'img',name:'img', sortable: false, searchable: false},
                {data: 'onMarket', name: 'onMarket'},
                {data: 'category', name: 'category'},
                {data: 'price', name: 'price'},
                {data: 'quantity', name: 'quantity'},
                {data: 'quantitySold', name: 'quantitySold'},
                {data: 'description', name: 'description'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
           
        });
        
    });

   
    var categoryTable ; 
    $('#v-pills-category-tab').on('click', function(e) {
        e.preventDefault()
        if(categoryTable){
            categoryTable.destroy();
        }
        categoryTable = $('#myCategory').DataTable({
            //"scrollY": "400px",
            //"scrollX": true,
            "scrollCollapse": true,
            "paging":         false,
            processing: true,
            serverSide: true,
            ajax: "./home/ajaxcategory",
            columns: [
                {data: 'check', name: 'check', orderable: false, searchable: false},
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'name', name: 'name'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            select: true,
        
        });
        
    })
    
    $.get("./home/ajaxcategory", function (data) {
        $.each(data.data,function (index,arr){
            $("#sel").append( '<option value="'+arr.name+'">'+arr.name+'</option>' )
        })
       console.log(data.data[0].name);
    })
    $('#sel').on('change', function(){
        console.log($(this).data('table'));
        let table_name = $(this).data('table');
        let table;
        if(table_name == 'products'){
            table = productTable;
        }        
        else if(table_name=='category'){
            table = categoryTable;
        }
        table.columns(5).search(this.value).draw();
    })
    $('input[name=chkAll').change(function () {
        let isChecked = $(this).prop('checked');
            $('#myProducts > tbody input:checkbox').prop('checked', isChecked);
    })

    $('#createNewProduct').click(function () {
        $('#saveBtn').val("create-product");
        $('#product_id').val('');
        $('#productForm').trigger("reset");
        $('#modelHeading').html("Create New Product");
        $('#ajaxModel').modal('show');
    });
    
    function checkboxAction(table_name,action){
        let table;
        if(table_name == 'products')
            table = productTable;
        else if(table_name=='category')
            table = categoryTable;
        let checkboxes= $('#myProducts > tbody input:checkbox')
        let arrCheckBox = [];
        $.each(checkboxes,function (index,status){
            if(status['checked']== true){
                arrCheckBox.push($(status).data('id'));
            }
        })
        $.ajax({
                type: "POST",
                url: "./onOrOff",
                data:{
                    'id':arrCheckBox,
                    'action': action
                    },
                success: function (data) {
                    table.draw();
                    return 'success';
                },
                error: function (data) {
                    return 'Error';
                }
            });
    }
    $('#onMarket').click(function () {
        checkboxAction($(this).data('table'),'Y');
    });
    $('#takeOff').click(function () {
        checkboxAction($(this).data('table'),'N');
    });

    $('body').on('click', '.editProduct', function () {
        let table;
        let table_name = $(this).data("table");
        let form_name ;
        let modal_name;
        let id = $(this).data("id");
        if(table_name == 'products'){
            table = productTable;
            form_name = '#productForm';
            modal_name = '#ajaxProductModel';
        }
        else if(table_name=='category'){
            table = categoryTable;
            form_name = '#categoryForm2';
            modal_name = '#ajaxCategoryModel';
        }
        console.log($(this).data("table"),modal_name,$(this).data("id"))
        if(table_name == 'products'){
            var product_id = $(this).data('id');
            $.get("./home/ajax"+table_name+'/'+ product_id +'/edit', function (data) {
                $('#productModelHeading').html("Edit Product");
                //$('#saveBtn').val("edit-user");
                $(form_name+' input[name=product_id]').val(data.productID);
                $(form_name+' input[name=name]').val(data.name);
                $(form_name+' input[name=category]').val(data.category);
                $(form_name+' input[name=price]').val(data.price);
                $(form_name+' input[name=quantity]').val(data.quantity);
                $(form_name+' input[name=quantitySold]').val(data.quantitySold);
                $(form_name+' textarea[name=description]').val(data.description);
                $(modal_name).modal('show');
            })
        }
        else if(table_name=='category'){
            $.get("./home/ajax"+table_name+'/'+ id +'/edit', function (data) {
                $('#categoryModelHeading').html("Edit Category");
                //$('#saveBtn').val("edit-user");
                $('#ajaxCategoryModel').modal('show');
                $('#categoryForm2 input[name=id]').val(data.id);
                $('#categoryForm2 input[name=name]').val(data.name);
                console.log(data);
            })
        }
      
   });
//save changes
   $('body').on('click',  '.saveBtn',function (e) {
         e.preventDefault();
         
        let table;
        let button = $(this);
        let table_name = $(this).data("table");
        let form_name ;
        let modal_name
        button.html('Sending..');
        if(table_name == 'products'){
            table = productTable;
            form_name = '#productForm';
            modal_name = '#ajaxProductModel';
        }
        else if(table_name=='category'){
            table = categoryTable;
            form_name = '#categoryForm2';
            modal_name = '#ajaxCategoryModel';
        }
            
        console.log($(this).data("table"),$(this).data("id"))
        console.log($(form_name).serialize());
        $.ajax({
          data: $(form_name).serialize(),
          url: "./home/ajax"+table_name,
          type: "POST",
          dataType: 'json',
          success: function (data) {
              $(form_name).trigger("reset");
              $(button).html('Save Changes');
              $(modal_name).modal('hide');
              table.draw();
          },
          error: function (data) {
              console.log('Error:', data);
              $(button).html('Save Changes');
          }
      });
    });

    
//delete
    $('body').on('click', '.deleteProduct', function () {
        let table;
        let table_name = $(this).data("table");
        let id = $(this).data("id");
        confirm("Are You sure want to delete !");
        if(table_name == 'products')
            table = productTable;
        else if(table_name=='category')
            table = categoryTable;
            
        $.ajax({
            type: "DELETE",
            //url: "{{ route('ajaxproducts.store') }}"+'/'+product_id,
            url: "./home/ajax"+table_name+'/'+id,
            success: function (data) {
                table.draw();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

    //---------------------- Category.js -------------------//
    $('#createNewCategory').click(function (e) {
        e.preventDefault();
        $(this).html('Sending..');
        console.log($('#categoryForm').serialize());
        $.ajax({
          data: $('#categoryForm').serialize(),
          url: "./home/ajaxcategory",
          type: "POST",
          dataType: 'json',
          success: function (data) {
              $('#createNewCategory').html('送出');
              $('#categoryForm').trigger("reset");
              categoryTable.draw();
              
               console.log('succ');
          },
          error: function (data) {
              console.log('Error:', data);
              
          }
      });
    });

}) 



    //   $.ajaxSetup({

    //       headers: {

    //           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

    //       }

    // });



   
