@extends('layouts.app')

@section('style')
        .thumbnail-container {
            display: flex;
            overflow-x: auto;
            white-space: nowrap;
            gap: 8px;
            padding-bottom: 8px;
        }

        .thumbnail-container::-webkit-scrollbar {
            height: 8px;
        }
        .thumbnail-container::-webkit-scrollbar-track {
            background: transparent;
        }
        .thumbnail-container::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 4px;
        }

        /* Default thumbnail size for larger screens */
        .thumbnail-img {
            width: 10%; 
            height: auto; 
            object-fit: contain;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.3s ease-in-out;
        }
        .thumbnail-img.active {
            border-color: #007bff;
        }

        /* Increase thumbnail size for mobile devices */
        @media (max-width: 767.98px) {
            .thumbnail-img {
                width: 15%;
            }
        }

        picture {
            width: 100%;
            aspect-ratio: 4 / 3; /* Adjust aspect ratio as needed */
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        
        picture img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

@endsection

@section('title')
    {{ $product->name }}
@endsection

@section('content')
    <!-- Main Product Display -->
    <section class="flex card mb-2">
        <div class="container p-4">
            <div class="row">
                <!-- Left Column: Main Image and Thumbnails -->
                <div class="col-12 col-lg-8 my-2">
                    @php
                        $images = json_decode($product->images, true);
                    @endphp
                    @if(is_array($images) && count($images) > 0)
                        @php
                            $defaultImage = $images[0];
                        @endphp
                        <!-- Main Image -->
                        <picture>
                            <img id="mainImage" src="{{ Storage::url($defaultImage) }}" alt="Product Image" width="100%" class="rounded border">
                        </picture>
                        <!-- Horizontal Thumbnail Container -->
                        <div class="thumbnail-container mt-2">
                            @foreach($images as $img)
                                <img src="{{ Storage::url($img) }}" alt="Thumbnail" class="img-thumbnail thumbnail-img" data-image="{{ Storage::url($img) }}">
                            @endforeach
                        </div>
                    @else
                        No Images Available
                    @endif
                </div>
                <!-- Right Column: Product Details -->
                <div class="col-12 col-lg-4 my-2 d-flex flex-column">
                    @php
                    if( $product->price <= 0) {
                        $pricetag = 'ฟรี!';
                    }else{
                        $pricetag = '฿' . $product->price;
                    }
                    @endphp
                    <h2 class="fw-light">{{ $product->name }}</h2>
                    <h1 class="fw-semibold">{{ $pricetag }}</h1>
                    
                    <div class="flex-grow-1"></div>
                    
                    <div class="d-flex gap-2">
                        @if ($product->price <= 0)
                            @if ($product->isDownloadable)
                                <a type="button" class="btn btn-success btn-lg" href="{{$product->download}}" download>ดาวน์โหลด</a>
                            @endif
                        @else
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $product->id }}">
                                <input type="hidden" name="name" value="{{ $product->name }}">
                                <input type="hidden" name="price" value="{{ $product->price }}">
                                <button type="submit" class="btn btn-primary btn-lg">เพิ่มใส่ตะกร้า</button>
                            </form>
                            <button type="button" class="btn btn-danger btn-lg">ซื้อเลย</button>
                            @if ($product->isDownloadable)
                                <a type="button" class="btn btn-success btn-lg" href="{{$product->download}}" download>ดาวน์โหลด</a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="flex card">
        <div class="container p-4">
            <p>{{ $product->description }}</p>
        </div>
    </section>

    <!-- JavaScript to Change Main Image on Thumbnail Click -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let thumbnails = document.querySelectorAll(".thumbnail-img");
            let mainImage = document.getElementById("mainImage");

            thumbnails.forEach(function(thumbnail) {
                thumbnail.addEventListener("click", function() {
                    // Update the main image src
                    let newSrc = this.getAttribute("data-image");
                    mainImage.src = newSrc;
                    // Update active state
                    thumbnails.forEach(function(img) { img.classList.remove("active"); });
                    this.classList.add("active");
                });
            });

            // Set the first thumbnail as active by default
            if (thumbnails.length > 0) {
                thumbnails[0].classList.add("active");
            }
        });
    </script>
@endsection
