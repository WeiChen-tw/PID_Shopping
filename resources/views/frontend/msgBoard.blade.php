@extends('layouts.app')

@section('content')
<div class="container">
    <div class="body row">
        <div class="col-md-2">
            安安
        </div>
        <div class="col-md-9">
            
        </div>
        <div class="col-md-1">
        </div>

    </div>
</div>
@push('scripts')
    <script src="{{ asset('js/user.js') }}"></script>
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    
@endpush
@endsection
