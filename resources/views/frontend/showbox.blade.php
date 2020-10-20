

@foreach ($products as $product)
<div class="product col-md-2 " style="padding-bottom: 1.25rem;">
    <div class="card  bg-default this-product" data-id="{{$product->productID}}">
        <h5 class="card-header">
            {{$product->name}}
        </h5>
        <div class="card-body">
        @if ($product->img)
            <img class="img-fluid" style="height:6rem"src="data:image/jpeg;base64,{{base64_encode($product->img)}}" >
            
        @else
            <h3 style="height:5.5rem" > No Image</h3>
        @endif
            <p class="card-text">
                
            </p>
        </div>
        <div class="card-footer ">
            <p>庫存量{{$product->quantity}}</p>
            <p>售出量{{$product->quantitySold}}</p>
        </div>
    </div>
</div>
@endforeach