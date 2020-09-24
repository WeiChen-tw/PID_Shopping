

@foreach ($products as $product)
<div class="product col-md-4 " style="padding-bottom: 1.25rem;">
    <div class="card bg-default">
        <h5 class="card-header">
            {{$product->name}}
        </h5>
        <div class="card-body">
            <img class="img-responsive" src="data:image/jpeg;base64,{{base64_encode($product->img)}}" >
            <p class="card-text">
                
            </p>
        </div>
        <div class="card-footer ">
            Card footer
        </div>
    </div>
</div>
@endforeach