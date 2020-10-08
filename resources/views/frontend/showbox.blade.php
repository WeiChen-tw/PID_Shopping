

@foreach ($products as $product)
<div class="product col-md-4 " style="padding-bottom: 1.25rem;">
    <div class="card  bg-default this-product" data-id="{{$product->productID}}">
        <h5 class="card-header">
            {{$product->name}}
        </h5>
        <div class="card-body">
        @if ($product->img)
            <img class="img-fluid" src="data:image/jpeg;base64,{{base64_encode($product->img)}}" >
            
        @else
            <h3> No Image</h3>
        @endif
            <p class="card-text">
                
            </p>
        </div>
        <div class="card-footer ">
            Card footer
        </div>
    </div>
</div>
@endforeach