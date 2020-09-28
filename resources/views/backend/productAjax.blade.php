<!DOCTYPE html>

<html>

<head>

    <title>Laravel 5.8 Ajax CRUD tutorial using Datatable - ItSolutionStuff.com</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

</head>

<body>



<div class="container">

    <h1>Laravel 5.8 Ajax CRUD tutorial using Datatable - ItSolutionStuff.com</h1>

    <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Create New Product</a>
    <a class="btn btn-primary" href="javascript:void(0)" id="onMarket">上架</a>
    <a class="btn btn-danger" href="javascript:void(0)" id="takeOff">下架</a>
    <select id="sel">
        <option value="">查詢分類</option>
        <option value="1">1</option>
        <option value="2">2</option>
    </select>
    <table id="myProducts" class="table table-bordered data-table">

        <thead>

            <tr>
                <th><input type="checkbox" name="chkAll" /> </th>
                <th>No</th>
                <th>Name</th>
                <th>Img</th>
                <th>onMarket</th>
                <th>Category</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>QuantitySold</th>
                <th>Descriptions</th>
                <th width="280px">Action</th>
            </tr>

        </thead>

        <tbody>

        </tbody>
        <tfoot>
            <tr>
                <th><input type="checkbox" name="chkAll" /> </th>
                <th>No</th>
                <th>Name</th>
                <th>Img</th>
                <th>onMarket</th>
                <th>Category</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>QuantitySold</th>
                <th>Descriptions</th>
                <th width="280px">Action</th>
            </tr>

        </tfoot>
    </table>

</div>



<div class="modal fade" id="ajaxModel" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title" id="modelHeading"></h4>

            </div>

            <div class="modal-body">

                <form id="productForm" name="productForm" class="form-horizontal">

                   <input type="hidden" name="product_id" id="product_id">

                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <!-- <div class="form-group">
                        <label for="img" class="col-sm-2 control-label">Img</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="img" name="img" placeholder="Enter Img" value="" maxlength="50" required="">
                        </div>
                    </div> -->

                    <div class="form-group">
                        <label for="category" class="col-sm-2 control-label">Category</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="category" name="category" placeholder="Enter Category" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="price" class="col-sm-2 control-label">Price</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="price" name="price" placeholder="Enter Price" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="quantity" class="col-sm-2 control-label">Quantity</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Enter Quantity" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="quantitySold" class="col-sm-2 control-label">QuantitySold</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="quantitySold" name="quantitySold" placeholder="Enter QuantitySold" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">

                        <label class="col-sm-2 control-label">Descriptions</label>

                        <div class="col-sm-12">

                            <textarea id="description" name="description" required="" placeholder="Enter Descriptions" class="form-control"></textarea>

                        </div>

                    </div>



                    <div class="col-sm-offset-2 col-sm-10">

                     <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes

                     </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>



</body>



<script type="text/javascript">

  $(function () {



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
        ajax: "{{ route('ajaxproducts.index') }}",
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
    var table = $('.data-table').DataTable();
    // table.columns().indexes().flatten().each( function ( i ) {
    // var column = table.column( i );
    // var select = $('<select><option value=""></option></select>')
    //     .appendTo( $(column.footer()).empty() )
    //     .on( 'change', function () {
    //         // Escape the expression so we can perform a regex match
    //         var val = $.fn.dataTable.util.escapeRegex(
    //             $(this).val()
    //         );
 
    //         column
    //             .search( val ? '^'+val+'$' : '', true, false )
    //             .draw();
    //     } );
 
    // column.data().unique().sort().each( function ( d, j ) {
    //     select.append( '<option value="'+d+'">'+d+'</option>' )
    // } );
// } );

        
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



  });

</script>

</html>
