@extends('layouts.app')

@section('title', 'แก้ไขสินค้า')

@section('content')
    <form method="POST" action="{{ route('productUpdate', $product->id) }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group py-4 text-center">
            <h1 class="text text-center"> แก้ไขรายละเอียดสินค้า </h1>
        </div>

        <div class="form-group py-2">
            <label for="name" class="py-2">ชื่อสินค้า</label>
            <input type="text" name="name" class="form-control" value="{{$product->name}}">
        </div>
        @error('name')
            <div class="ms-2 p-2">
                <span class="text text-danger">{{ $message }}</span>
            </div>
        @enderror

        <div class="form-group py-2">
            <label for="price" class="py-2">ราคา</label>
            <div class="input-group">
                <input type="number" name="price" class="form-control" value="{{$product->price}}">
                <div class="input-group-append">
                    <span class="input-group-text" style="font-family:NotoColorEmoji-Regular;">🪙</span>
                </div>
            </div>
        </div>
        @error('price')
            <div class="ms-2 p-2">
                <span class="text text-danger">{{ $message }}</span>
            </div>
        @enderror

        <div class="form-group py-2">
            <label for="description" class="py-2">คำอธิบายสินค้า</label>
            <textarea name="description" class="form-control" cols="30" rows="5">{{$product->description}}</textarea>
        </div>

        <div class="form-group py-2">
            <div class="py-2">
                <label for="download" class="py-2">ลิ้งค์ดาวห์โหลด</label>
                <input type="text" name="download" class="form-control">
            </div>

            <div class="py-2">
                <input class="form-check-input" name="isDownloadable" type="checkbox" {{$product->isDownloadable ? 'checked' : ''}}>
                <label for="isDownloadable" class="form-check-label">ดาวน์โหลดได้?</label>
            </div>
        </div>

        <div class="form-group py-2">
            <input class="form-check-input" name="isBuyable" type="checkbox" {{$product->isBuyable ? 'checked' : ''}}>
            <label for="isBuyable" class="form-check-label">ซื้อได้?</label>
        </div>

        <div class="form-group py-2">
            <input class="form-check-input" name="isPublish" type="checkbox" {{$product->isPublish ? 'checked' : ''}}>
            <label for="isPublish" class="form-check-label">เผยแพร่?</label>
        </div>

        <div class="form-group py-2">
            <label for="images[]" class="form-check-label">ภาพประกอบ</label><br>
            <input 
                type="file" 
                name="images[]" 
                accept="image/*" 
                multiple 
                onchange="previewImages(event)"
            >
            
            <!-- Display existing and new images -->
            <div id="preview-container" class="flex gap-2 mt-2 border p-2" style="display: flex; flex-wrap: wrap;">
                @php
                    $images = json_decode($product->images, true);
                @endphp
                @if (!empty($images))
                    @foreach ($images as $image)
                        <div class="image-wrapper" data-name="{{ $image }}" style="position: relative; display: inline-block; margin: 5px;">
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
            </div>

            <input type="hidden" name="image_order" id="image_order">
        </div>

        <div class="form-group py-2">
            <input type="submit" value="บันทึก" class="btn btn-primary py-2">
        </div>
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Sortable for drag-and-drop
            new Sortable(document.getElementById('preview-container'), {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function () {
                    updateImageOrder();
                }
            });

            // Handle delete button clicks
            document.getElementById('preview-container').addEventListener('click', function(e) {
                if (e.target.classList.contains('delete-btn')) {
                    const wrapper = e.target.closest('.image-wrapper');
                    wrapper.remove();
                    updateImageOrder();
                }
            });

            // Initial order update
            updateImageOrder();
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
            updateImageOrder();
        }

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
    </script>
@endsection
