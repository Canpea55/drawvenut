<style>
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

    .banner-image {
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
    }

    .overlay-text {
        bottom: 10px;
        left: 10px;
        background: rgba(0, 0, 0, 0.6);
        border-radius: 5px;
    }
</style>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.4/imagesloaded.pkgd.min.js"></script>
    @yield('script')
</head>

<body>
    <!-- Main Product Display -->
    <section class="card mb-2 position-relative">
        <div class="p-4">
            @php
                $images = json_decode($product->images, true);
                $defaultImage = is_array($images) && count($images) > 0 ? $images[0] : null;
            @endphp
            
            <div class="position-relative">
                <img 
                    src="{{ Storage::url($defaultImage) }}" 
                    class="w-100 banner-image"
                    alt="Product Image">

                <!-- Text Overlay -->
                <div class="position-absolute text-white p-2 overlay-text">
                    <h4 class="m-0">{{ $product->name }}</h4>
                </div>
            </div>

            {{-- <div class="d-flex justify-content-between">
                <div>
                    <p class="text-info-emphasis" style="margin-bottom: 0;">สถานะ</p>
                    <h1 class="text-info" style="margin-top: 0;">รอตรวจสอบบิล</h1>
                </div>

                <button class="btn btn-sm btn-info align-self-center" id="paymentToggler" onclick="paymentToggle()">อัปโหลดสลิปใหม่</button>
            </div> --}}

            <div class="d-flex justify-content-start mt-3 gap-1">
            @if ($product->isDownloadable)
                <a class="btn btn-success btn-lg " href="{{ $product->download }}" download>ดาวน์โหลด</a>
            @else
                <a class="btn btn-success btn-lg disabled" role="button" aria-disabled="true">ดาวน์โหลด</a>
            @endif
                <a href="{{route('product', $product->id)}}" class="btn btn-lg align-self-center" target="_top">หน้าร้านค้า</a>
            </div>
        </div>
    </section>    

    <section class="flex card">
        {{-- <div class="m-4">
                <!-- Main Image -->
                
                <!-- Horizontal Thumbnail Container -->
                <div class="thumbnail-container mt-2">
                    @foreach($images as $img)
                        <img src="{{ Storage::url($img) }}" alt="Thumbnail" class="img-thumbnail thumbnail-img"
                            data-image="{{ Storage::url($img) }}">
                    @endforeach
                </div>
        </div> --}}
        
        <div class="container p-4">
            {!! $product->description !!}
        </div>
    </section>

    <!-- JavaScript to Change Main Image on Thumbnail Click -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let thumbnails = document.querySelectorAll(".thumbnail-img");
            let mainImage = document.getElementById("mainImage");

            thumbnails.forEach(function (thumbnail) {
                thumbnail.addEventListener("click", function () {
                    // Update the main image src
                    let newSrc = this.getAttribute("data-image");
                    mainImage.src = newSrc;
                    // Update active state
                    thumbnails.forEach(function (img) { img.classList.remove("active"); });
                    this.classList.add("active");
                });
            });

            // Set the first thumbnail as active by default
            if (thumbnails.length > 0) {
                thumbnails[0].classList.add("active");
            }
        });
    </script>
</body>