<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <title>Laravel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->

    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .header{
            height: 6.25rem;
        }
        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

    <div class=" container-fluid position-ref full-height">


            <div class="row header" >

                    @if (Route::has('login'))
                        <div class="col-md-12 top-right links">
                            @auth
                                <a href="{{ url('/home') }}">購物車</a>
                                @else
                                    <a href="{{ route('login') }}">Login</a>
                                    <a href="{{ route('register') }}">Register</a>
                                    @endauth
                        </div>
                    @endif



            </div>
            <div class="row">
                    <div class="col-md-12">
                        <nav class="navbar navbar-expand-lg navbar-light bg-light">

                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                                <span class="navbar-toggler-icon"></span>
                            </button> <a class=" brand" href="#">Brand</a>
                            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                <ul class="navbar-nav">
                                    <li class="nav-item active">
                                        <a class="nav-link" href="#">Link <span class="sr-only">(current)</span></a>
                                    </li>

                                    <!-- <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown">Dropdown link</a>
                                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                            <a class="dropdown-item" href="#">Action</a> <a class="dropdown-item" href="#">Another action</a> <a class="dropdown-item" href="#">Something else here</a>
                                            <div class="dropdown-divider">
                                            </div> <a class="dropdown-item" href="#">Separated link</a>
                                        </div>
                                    </li> -->
                                </ul>
                                <form class="form-inline">
                                    <input class="form-control mr-sm-2" type="text" />
                                    <button class="btn btn-primary my-2 my-sm-0" type="submit">
                                        Search
                                    </button>
                                </form>

                            </div>
                        </nav>
                        <div class="jumbotron">
                            <h2>
                                線上商城
                            </h2>

                        </div>
                    </div>
            </div>
            <div id ="showBox" class="row pre-scrollable ">
                    @section('showBox')
                    @show
            </div>

    </div>
    <!-- Discount Modal -->
    <div class="modal fade" id="shoppingCartModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shoppingCartModelHeading">放入購物車</h4>
                </div>
                <div class="modal-body">
                    <form id="shopCartForm" name="shopCartForm" class="form-horizontal">
                        <input type="hidden" name="id" id="id">


                        <div class="form-group">
                            <label name="name" class="col-sm-6 control-label">品名</label>
                           
                        </div>
                        <div class="form-group">
                            <label name="price" class="col-sm-6 control-label">價格</label>
                        </div>
                        <div class="form-group">
                            <label name="sys_quantity" class="col-sm-6 control-label">庫存量</label>
                        </div>

                        <div class="form-group">
                            <label name="quantitySold" class="col-sm-6 control-label">售出量</label>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">商品介紹</label>
                            <div class="col-sm-12">
                                <textarea id="description" name="description" required="" placeholder="Enter Descriptions" class="form-control" readonly></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="quantity" class="col-sm-4 control-label">購買數量</label>
                            <div class="col-sm-12">
                                <input type="number" oninput = "value=value.replace(/[^\d]/g,'')" class="form-control" id="" name="quantity" placeholder="" value="1" maxlength="50" required="">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-primary mb-2 saveBtn" href="javascript:void(0)" data-table="" id=""> 儲存</a>
                    <a class="btn btn-danger mb-2 closeModal" href="javascript:void(0)" id=""> 關閉</a>
                </div>
            </div>
        </div>
    </div>
    <!-- END -->

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="{{ asset('js/frontend.js') }}"></script>
</body>
</html>
