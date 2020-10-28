@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-md-center mt-5">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">註冊</div>
                <div class="card-body">
                    <form role="form" method="POST" action="{{ url('/register') }}">
                        {!! csrf_field() !!}

                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">名稱</label>

                            <div class="col-lg-6">
                                <input
                                        type="text"
                                        class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                        name="name"
                                        value="{{ old('name') }}"
                                        required
                                >
                                @if ($errors->has('name'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">電子信箱</label>

                            <div class="col-lg-6">
                                <input
                                        type="email"
                                        class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                >

                                @if ($errors->has('email'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">地址</label>

                            <div class="col-lg-6">
                                <input
                                        type="text"
                                        class="form-control{{ $errors->has('addr') ? ' is-invalid' : '' }}"
                                        name="addr"
                                        value="{{ old('addr') }}"
                                        required
                                >
                                @if ($errors->has('addr'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('addr') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">手機號碼</label>

                            <div class="col-lg-6">
                                <input
                                        type="text"
                                        class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        required
                                >
                                @if ($errors->has('phone'))
                                        <div class="col-md-12">
                                            <strong class="text-danger">{{ $errors->first('phone') }}</strong>
                                        </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">密碼</label>

                            <div class="col-lg-6">
                                <input
                                        type="password"
                                        class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                        name="password"
                                        required
                                >
                                @if ($errors->has('password'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">確認密碼</label>

                            <div class="col-lg-6">
                                <input
                                        type="password"
                                        class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                                        name="password_confirmation"
                                        required
                                >
                                @if ($errors->has('password_confirmation'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('captcha') ? 'has-error' : ''}}">
                            <label class="col-lg-4 col-form-label text-lg-right">驗證碼</label>
        
                            <div class="col-md-4">
                                <input id="captcha" class="" name="captcha" autocomplete="off">
                                <img class="{{$errors->has('captcha')?'parsley-error':''}}" src="{{captcha_src('flat')}}" style="cursor: pointer" onclick="this.src='{{captcha_src('flat')}}'+Math.random()">
                            
                            </div>
                            <div class="col-md-4">
                                </div>
                            @if($errors->has('captcha'))
                                <div class="col-md-12">
                                    <p class="text-danger text-left"><strong>{{$errors->first('captcha')}}</strong></p>
                                </div>
                            @endif
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-6 offset-lg-4">
                                <button type="submit" class="btn btn-primary">
                                    註冊
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
