@extends('layouts.app')

@section('title', 'ฟอร์มสินค้า')

@section('content')
    <form method="POST" action="insert" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group py-4 text-center">
            <h1 class="text text-center"> ฟอร์มสินค้า </h1>
            <input class="form-check-input" name="createMultiple" type="checkbox">
            <label for="createMultiple" class="form-check-label">เพิ่มหลายอัน</label>
        </div>

        <div class="form-group py-2">
            <label for="name" class="py-2">ชื่อสินค้า</label>
            <input type="text" name="name" class="form-control">
        </div>
        @error('name')
            <div class="ms-2 p-2">
                <span class="text text-danger">{{ $message }}</span>
            </div>
        @enderror

        <div class="form-group py-2">
            <label for="price" class="py-2">ราคา</label>
            <div class="input-group">
                <input type="number" name="price" class="form-control" placeholder="10">
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
            <textarea name="description" class="form-control" cols="30" rows="5"></textarea>
        </div>

        <div class="form-group py-2">
            <div class="py-2">
                <label for="download" class="py-2">ลิ้งค์ดาวห์โหลดสินค้า</label>
                <input type="text" name="download" class="form-control">
            </div>
            
            <div class="py-2">
                <input class="form-check-input" name="isDownloadable" type="checkbox" value="1" {{old('isBuyable') ? 'checked' : ''}}>
                <label for="isDownloadable" class="form-check-label">ดาวน์โหลดได้?</label>
            </div>
        </div>
        
        
        <div class="form-group py-2">
            <input class="form-check-input" name="isBuyable" type="checkbox" value="1" {{old('isBuyable') ? 'checked' : ''}}>
            <label for="isBuyable" class="form-check-label">ซื้อได้?</label>
        </div>

        
        <div class="form-group py-2">
            <input class="form-check-input" name="isPublish" type="checkbox" value="1" {{old('isBuyable') ? 'checked' : ''}}>
            <label for="isPublish" class="form-check-label">เผยแพร่?</label>
        </div>

        <div class="form-group py-2">
            <label for="images[]" class="form-check-label">ภาพประกอบ</label><br>
            <input type="file" name="images[]" accept="image/*" multiple onchange="previewImages(event)">
            
            <!-- Images will be displayed here and can be dragged to rearrange -->
            <div id="preview-container" class="flex gap-2 mt-2 border p-2" style="display: flex; flex-wrap: wrap;"></div>
            
            <!-- Hidden input to store the reordered image paths -->
            <input type="hidden" name="image_order" id="image_order">
        </div>
        
        <div class="form-group py-2">
            <input type="submit" value="บันทึก" class="btn btn-primary py-2">
        </div>
    </form>

    <!-- Include SortableJS for Drag and Drop -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

    <script>
        function previewImages(event) {
            let previewContainer = document.getElementById("preview-container");
            previewContainer.innerHTML = ""; // Clear previous previews
            
            Array.from(event.target.files).forEach((file, index) => {
                let reader = new FileReader();
                reader.onload = function(e) {
                    let imgWrapper = document.createElement("div");
                    imgWrapper.classList.add("image-wrapper");
                    imgWrapper.setAttribute("data-name", file.name); // Store the file name
                    imgWrapper.style.position = "relative";
                    imgWrapper.style.display = "inline-block";
                    imgWrapper.style.margin = "5px";
                    
                    let img = document.createElement("img");
                    img.src = e.target.result;
                    img.width = 100;
                    img.classList.add("rounded", "border");

                    let deleteBtn = document.createElement("button");
                    deleteBtn.innerHTML = "❌";
                    deleteBtn.style.position = "absolute";
                    deleteBtn.style.top = "0";
                    deleteBtn.style.right = "0";
                    deleteBtn.style.background = "white";
                    deleteBtn.style.color = "white";
                    deleteBtn.style.border = "none";
                    deleteBtn.style.cursor = "pointer";
                    deleteBtn.addEventListener("click", function() {
                        imgWrapper.remove();
                        updateImageOrder(); // Update order when an image is removed
                    });

                    imgWrapper.appendChild(img);
                    imgWrapper.appendChild(deleteBtn);
                    previewContainer.appendChild(imgWrapper);
                };
                reader.readAsDataURL(file);
            });

            // Enable Drag-and-Drop Sorting
            new Sortable(previewContainer, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function () {
                    updateImageOrder(); // Update order when sorting is finished
                }
            });
        }

        function updateImageOrder() {
            let orderArray = [];
            document.querySelectorAll("#preview-container .image-wrapper").forEach(wrapper => {
                orderArray.push(wrapper.getAttribute("data-name"));
            });
            document.getElementById("image_order").value = JSON.stringify(orderArray);
        }
    </script>


@endsection
