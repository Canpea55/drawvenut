@extends('layouts.app')

@section('title')
    @if (empty($product))
        เพิ่มสินค้า
    @else
        แก้ไขสินค้า
    @endif
@endsection

@section('head-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script>
        // Preload existing tags. This assumes that $product->tags is a collection.
        // If there are no tags, it defaults to an empty array.
        var preloadedTags = {!! json_encode(!empty($product) ? $product->tags->pluck('name') : []) !!};
    </script>
@endsection
@section('foot-script')
    <script>
        $(document).ready(function(){
            let tags = preloadedTags || [];
            const $container = $('#tags-container');
            const $input = $('#tag-input');
            const $hidden = $('#tags-hidden');
            let typingTimer;
            const typingDelay = 150; // delay in ms for ajax call
            let selectedIndex = -1; // tracks current selection in suggestion list

            // Create suggestion container (if not already in HTML)
            if($('#tag-suggestions').length === 0){
                $container.append('<ul id="tag-suggestions" class="list-group position-absolute w-100 mt-0" style="top: 100%; left:0%; z-index: 9999;"></ul>');
            }
            const $suggestions = $('#tag-suggestions');

            // Function to update the hidden field; tags saved as a space–separated string
            function updateHidden(){
                $hidden.val(tags.join(' '));
            }

            // Function to create and append a badge for a tag
            function addBadge(tag) {
                // Create a badge element with a delete button inside
                const badge = $(`
                    <span class="badge bg-primary me-1 tag-badge d-flex align-items-center gap-1">
                        <div>${tag}</div>
                        <button type="button" class="btn-close btn-close-white btn-sm" aria-label="Remove"></button>
                    </span>
                `);
                // When the delete button is clicked, remove the badge and update tags
                badge.find('button').on('click', function(e) {
                    e.stopPropagation();
                    tags = tags.filter(t => t !== tag);
                    badge.remove();
                    updateHidden();
                });
                // Insert the badge before the input field
                badge.insertBefore($input);
            }

            // Preload the badges for the existing tags
            tags.forEach(tag => addBadge(tag));
            updateHidden();

            // Function to display suggestions
            function showSuggestions(suggestions){
                $suggestions.empty();
                selectedIndex = -1; // reset selection

                // Filter suggestions to remove those already added
                suggestions = suggestions.filter(sTag => !tags.includes(sTag));

                if(suggestions && suggestions.length){
                    suggestions.forEach(function(sTag){
                        const item = $(`<a href="#" class="list-group-item list-group-item-action">${sTag}</a>`);
                        item.on('click', function(e){
                            e.preventDefault();
                            // Add suggestion as a tag if not already added
                            if(!tags.includes(sTag)){
                                tags.push(sTag);
                                addBadge(sTag);
                                updateHidden();
                            }
                            $suggestions.empty();
                            $input.val('');
                        });
                        $suggestions.append(item);
                    });
                }
            }

            // Listen for keydown events on the input field for navigation
            $input.on('keydown', function(e){
                // Handle arrow keys for suggestion navigation
                if(e.key === 'ArrowDown' || e.key === 'ArrowUp'){
                    e.preventDefault(); // prevent default cursor move
                    let items = $suggestions.find('a');
                    if(items.length){
                        if(e.key === 'ArrowDown'){
                            selectedIndex = (selectedIndex + 1) % items.length;
                        } else if(e.key === 'ArrowUp'){
                            selectedIndex = selectedIndex <= 0 ? items.length - 1 : selectedIndex - 1;
                        }
                        items.removeClass('active');
                        $(items[selectedIndex]).addClass('active');
                    }
                    return; // exit keydown handler for these keys
                }
                // Handle Enter key to select the active suggestion if any
                if(e.key === 'Enter'){
                    let items = $suggestions.find('a');
                    if(selectedIndex >= 0 && items.length > 0){
                        e.preventDefault();
                        $(items[selectedIndex]).trigger('click');
                        selectedIndex = -1;
                        return;
                    }
                }
                // When space is pressed, convert the typed text into a badge
                if(e.key === ' '){
                    e.preventDefault(); // prevent the space character from being added
                    let text = $input.val().trim();
                    if(text){
                        if(!tags.includes(text)){
                            tags.push(text);
                            addBadge(text);
                        }
                        $input.val(''); // clear the input field
                        updateHidden();
                        $suggestions.empty();
                    }
                }
                // If input is empty and Backspace is pressed, remove the last badge
                else if(e.key === 'Backspace'){
                    if(!$input.val()){
                        if(tags.length > 0){
                            tags.pop();
                            $container.find('.tag-badge').last().remove();
                            updateHidden();
                        }
                    }
                }
            });

            // Listen for keyup to trigger AJAX live search for tags
            $input.on('keyup', function(e){
                // Skip if space, backspace, arrow keys or enter because they're handled above
                if(e.key === ' ' || e.key === 'Backspace' || e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter'){
                    return;
                }
                clearTimeout(typingTimer);
                let query = $input.val().trim();
                if(query.length > 1){
                    typingTimer = setTimeout(function(){
                        // Adjust the URL/route as needed
                        $.ajax({
                            url: "{{ route('tags.search') }}",
                            method: "GET",
                            data: { query: query },
                            success: function(response){
                                // Expecting response like ['tag1', 'tag2', ...]
                                showSuggestions(response);
                            },
                            error: function(xhr, status, error){
                                console.error("Tag live search error:", error);
                                $suggestions.empty();
                            }
                        });
                    }, typingDelay);
                }
                else{
                    $suggestions.empty();
                }
            });
        });
    </script>
    <script>
        function updateImageOrder() {
            const orderArray = [];
            document.querySelectorAll("#preview-container .image-wrapper").forEach(wrapper => {
                if (wrapper.dataset.id) {
                    orderArray.push(`${wrapper.dataset.id}`);
                } else if (wrapper.dataset.name) {
                    orderArray.push(`${wrapper.dataset.name}`);
                }
            });
            document.getElementById('image_order').value = JSON.stringify(orderArray);
        }

        function highlightThumbnail() {
            const previewContainer = document.getElementById('preview-container');
            const mainImageBorder = document.getElementById('mainImage-border');
            const mainImage = document.getElementById('mainImage');
            const imageWrappers = previewContainer.querySelectorAll('.image-wrapper');
            if (imageWrappers.length > 0) {
                imageWrappers.forEach(wrapper => {
                    wrapper.querySelector('img').classList.remove('border-primary');
                });
                const firstImage = imageWrappers[0].querySelector('img');
                firstImage.classList.add('border-primary');
                mainImageBorder.classList.add('border-primary');
                mainImage.style.display = 'block';
                document.getElementById('mainImage').src = firstImage.src;
            } else {
                document.getElementById('mainImage').src = '';
                mainImageBorder.classList.remove('border-primary');
                mainImage.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Sortable for drag-and-drop
            new Sortable(document.getElementById('preview-container'), {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function () {
                    updateImageOrder();
                    highlightThumbnail();
                }
            });

            // Handle delete button clicks
            document.getElementById('preview-container').addEventListener('click', function(e) {
                if (e.target.classList.contains('delete-btn')) {
                    const wrapper = e.target.closest('.image-wrapper');
                    wrapper.remove();
                    updateImageOrder();
                    highlightThumbnail();
                }
            });

            // Initial order update and highlight
            updateImageOrder();
            highlightThumbnail();
        });

        function previewImages(event) {
            const previewContainer = document.getElementById("preview-container");
            previewContainer.innerHTML = ""; // Clear previous previews

            Array.from(event.target.files).forEach((file) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgWrapper = document.createElement("div");
                    imgWrapper.classList.add("image-wrapper");
                    imgWrapper.setAttribute("data-name", file.name);
                    imgWrapper.style.position = "relative";
                    imgWrapper.style.margin = "5px";

                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.width = 100;
                    img.classList.add("rounded", "border");

                    const deleteBtn = document.createElement("button");
                    deleteBtn.innerHTML = "❌";
                    deleteBtn.className = "delete-btn";
                    deleteBtn.style.position = "absolute";
                    deleteBtn.style.top = "0";
                    deleteBtn.style.right = "0";
                    deleteBtn.style.background = "transparent";
                    deleteBtn.style.border = "none";
                    deleteBtn.style.cursor = "pointer";

                    imgWrapper.appendChild(img);
                    imgWrapper.appendChild(deleteBtn);
                    previewContainer.appendChild(imgWrapper);
                };
                reader.readAsDataURL(file);
            });
        }
    </script>
@endsection

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
        height: 400px;
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

@section('content')
    @if (empty($product))
        <form method="POST" action="{{ route('product.insert')}}" enctype="multipart/form-data">
    @else
        <form method="POST" action="{{ route('product.update', $product->id) }}" enctype="multipart/form-data">
    @endif
        @csrf
        <!-- Main Product Display -->
        <section class="flex card mb-2">
            <div class="container p-4">
                <div class="row">
                    <!-- Left Column: Main Image and Thumbnails -->
                    <div class="col-12 col-lg-8 my-2">

                        <!-- Main Image -->
                        <label for="mainImage" class="form-check-label">ภาพปก</label>
                        <picture id="mainImage-border" class="rounded border">
                            <img id="mainImage" src="" alt="Product Image" width="100%" onerror="this.style.display='none'">
                        </picture>

                        <div class="form-group mt-2">
                            <label for="images[]" class="form-check-label">ภาพประกอบ</label>
                            
                            <!-- Display existing and new images -->
                            <div id="preview-container" class="flex gap-2 border p-2" style="display: flex; flex-wrap: wrap;">
                                @if (!empty($product))
                                    @php
                                        $images = json_decode($product->images, true);
                                    @endphp
                                    @if (!empty($images))
                                        @foreach ($images as $image)
                                            <div class="image-wrapper" data-name="{{ $image }}" style="position: relative; display: inline-block;">
                                                <img src="{{Storage::url($image)}}" width="100" class="rounded border">
                                                <button 
                                                    style="position: absolute; top: 0px; right: 0px; background: white; color: white; border: none; cursor: pointer;" 
                                                    type="button" 
                                                    class="delete-btn">
                                                    ❌
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                @endif
                            </div>

                            <input 
                                class="form-control mt-2"
                                type="file" 
                                name="images[]" 
                                accept="image/*" 
                                multiple 
                                onchange="previewImages(event);
                                timeoutUpdateImageOrder = setTimeout(updateImageOrder, 100);
                                timeoutHighlight = setTimeout(highlightThumbnail, 100);"
                            >
                
                            <input type="hidden" name="image_order" id="image_order">
                        </div>
                    </div>

                    <!-- Right Column: Product Details -->
                    <div class="col-12 col-lg-4 my-2 d-flex flex-column">

                        <div class="form-group py-2">
                            <label for="name" class="">ชื่อสินค้า</label>
                            @if (!empty($product))
                                <input type="text" name="name" class="form-control" value="{{$product->name}}">
                            @else
                                <input type="text" name="name" class="form-control">
                            @endif
                        </div>
                        @error('name')
                            <div class="ms-2 p-2">
                                <span class="text text-danger">{{ $message }}</span>
                            </div>
                        @enderror
                        
                        <div class="form-group py-2">
                            <label for="price" class="">ราคา</label>
                            <div class="input-group">
                                @if (!empty($product))
                                    <input type="number" name="price" class="form-control" value="{{$product->price}}">
                                @else
                                    <input type="number" name="price" class="form-control">
                                @endif
                                <div class="input-group-append">
                                    <span class="input-group-text" style="font-family:NotoColorEmoji-Regular;">฿</span>
                                </div>
                            </div>
                        </div>
                        @error('price')
                            <div class="ms-2 p-2">
                                <span class="text text-danger">{{ $message }}</span>
                            </div>
                        @enderror

                        <!-- filepath: d:\Projects\mediaroom-reclone\mediaroom\resources\views\admin\products\form.blade.php -->
                        <div class="form-group py-2">
                            <label for="tags-input">แท็ก</label>
                            <div id="tags-container" class="form-control position-relative" style="min-height: 40px; display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem;">
                                <input id="tag-input" type="text" class="bg-transparent" style="border: none; outline: none; flex: 1;" placeholder="พิมพ์แท็กแล้วกด space">
                            </div>
                            <input type="hidden" name="tags" id="tags-hidden">
                        </div>

                        {{-- fill empty space --}}
                        <div class="flex-grow-1"></div>

                        <div class="form-group py-2">
                            <div class="py-2">
                                <label for="download" class="">ลิ้งค์ดาวห์โหลดสินค้า</label>
                                @if (!empty($product))
                                    <input type="text" name="download" class="form-control" value="{{$product->download}}">
                                @else
                                    <input type="text" name="download" class="form-control">
                                @endif
                            </div>
                
                            <div class="py-2">
                                @if (!empty($product))
                                    <label class="switch">
                                        <input class="form-check-input" name="isDownloadable" type="checkbox" {{$product->isDownloadable ? 'checked' : ''}}>
                                        <span class="slider"></span>
                                    </label>
                                @else
                                    <label class="switch">
                                        <input class="form-check-input" name="isDownloadable" type="checkbox">
                                        <span class="slider"></span>
                                    </label>
                                @endif
                                <label for="isDownloadable" class="form-check-label align-top">ดาวน์โหลดได้</label>
                            </div>
                        </div>

                        <div class="form-group py-2">
                            <div>
                                @if (!empty($product))
                                    <label class="switch">
                                        <input class="form-check-input" name="isBuyable" type="checkbox" {{$product->isBuyable ? 'checked' : ''}}>
                                        <span class="slider"></span>
                                    </label>
                                @else
                                    <label class="switch">
                                        <input class="form-check-input" name="isBuyable" type="checkbox">
                                        <span class="slider"></span>
                                    </label>
                                @endif
                                <label for="isBuyable" class="form-check-label align-top">ซื้อได้</label>
                            </div>
                            <div>
                                @if (!empty($product))
                                    <label class="switch">
                                        <input class="form-check-input" name="isPublish" type="checkbox" {{$product->isPublish ? 'checked' : ''}}>
                                        <span class="slider"></span>
                                    </label>
                                @else
                                    <label class="switch">
                                        <input class="form-check-input" name="isPublish" type="checkbox">
                                        <span class="slider"></span>
                                    </label>
                                @endif
                                <label for="isPublish" class="form-check-label align-top">เผยแพร่</label>
                            </div>
                        </div> 
                        
                        @if (!empty($product))
                            <input type="submit" value="อัพเดตสินค้า" class="btn btn-primary mt-2">
                        @else
                            <input type="submit" value="เพิ่มสินค้าใหม่" class="btn btn-primary mt-2">
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="flex card p-4">
            <div class="form-group">
                <label for="description" class="">คำอธิบายสินค้า</label>
                <textarea id="tinymce" name="description" class="form-control" cols="30" rows="5">
                    @if (!empty($product))
                        {!!$product->description!!}
                    @endif
                </textarea>
            </div>
        </section>

    </form>
@endsection