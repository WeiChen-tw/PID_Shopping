

@foreach ($products as $product)
<div class="col-md-4">
    <div class="card bg-default">
        <h5 class="card-header">
            {{$product->name}}
        </h5>
        <div class="card-body">
            <img class="img-responsive" src="data:image/jpeg;base64,{{base64_encode($product->img)}}" >
            <p class="card-text">
                
            </p>
        </div>
        <div class="card-footer">
            Card footer
        </div>
    </div>
</div>
@endforeach