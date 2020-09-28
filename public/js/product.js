
function test(){
    console.log('test');
}
$(document).ready(function(){

       $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('.data-table').DataTable({
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

    $('#sel').on('change', function(){
        console.log(this.value);
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

    function checkboxAction(action){
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
        checkboxAction('Y');
    });
    $('#takeOff').click(function () {
        checkboxAction('N');
    });

    $('body').on('click', '.editProduct', function () {
        console.log($(this).data('id'));
      var product_id = $(this).data('id');
      $.get("{{ route('ajaxproducts.index') }}" +'/' + product_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Product");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal('show');
          $('#product_id').val(data.productID);
          $('#name').val(data.name);
          $('#category').val(data.category);
          $('#price').val(data.price);
          $('#quantity').val(data.quantity);
          $('#quantitySold').val(data.quantitySold);
          $('#description').val(data.description);
      })
   });



    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html('Sending..');
        console.log($('#productForm').serialize());
        $.ajax({
          data: $('#productForm').serialize(),
          url: "{{ route('ajaxproducts.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
              $('#productForm').trigger("reset");
              $('#ajaxModel').modal('hide');
              table.draw();
               console.log('succ');
          },
          error: function (data) {
              console.log('Error:', data);
              $('#saveBtn').html('Save Changes');
          }
      });
    });

    $('body').on('click', '.deleteProduct', function () {
        var product_id = $(this).data("id");
        confirm("Are You sure want to delete !");

        $.ajax({
            type: "DELETE",
            url: "{{ route('ajaxproducts.store') }}"+'/'+product_id,
            success: function (data) {
                table.draw();
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



   
