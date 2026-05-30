<input type="file" id="imageInput" accept="image/*">
<img id="selectedImage" src="" alt="Selected Image">
<button id="cropButton" disabled>Crop</button>




<script>
  const imageInput = document.getElementById('imageInput');
  const selectedImage = document.getElementById('selectedImage');
  const cropButton = document.getElementById('cropButton');
  let originalImage;
  let canvas;
  let context;
  let isCropping = false;
  let startX, startY, endX, endY;

  imageInput.addEventListener('change', handleImageUpload);
  cropButton.addEventListener('click', handleCrop);

  function handleImageUpload(event) {
    const file = event.target.files[0];

    if (file) {
      const reader = new FileReader();

      reader.onload = function(e) {
        selectedImage.src = e.target.result;
        originalImage = new Image();
        originalImage.src = e.target.result;

        originalImage.onload = function() {
          // Enable the crop button when the image is loaded
          cropButton.disabled = false;
        };
      };

      reader.readAsDataURL(file);
    }
  }

  selectedImage.addEventListener('mousedown', startCrop);
  selectedImage.addEventListener('mousemove', updateCrop);
  selectedImage.addEventListener('mouseup', endCrop);

  function startCrop(e) {
    if (!isCropping) {
      isCropping = true;
      startX = e.clientX - selectedImage.getBoundingClientRect().left;
      startY = e.clientY - selectedImage.getBoundingClientRect().top;
      canvas = document.createElement('canvas');
      canvas.width = selectedImage.width;
      canvas.height = selectedImage.height;
      context = canvas.getContext('2d');
      context.drawImage(selectedImage, 0, 0);
    }
  }

  function updateCrop(e) {
    if (isCropping) {
      endX = e.clientX - selectedImage.getBoundingClientRect().left;
      endY = e.clientY - selectedImage.getBoundingClientRect().top;
      context.clearRect(0, 0, canvas.width, canvas.height);
      context.drawImage(selectedImage, 0, 0);
      context.strokeStyle = 'red';
      context.lineWidth = 2;
      context.strokeRect(startX, startY, endX - startX, endY - startY);
    }
  }

  function endCrop() {
    if (isCropping) {
      isCropping = false;
      const croppedImageData = context.getImageData(
        startX,
        startY,
        endX - startX,
        endY - startY
      );
      const croppedCanvas = document.createElement('canvas');
      croppedCanvas.width = endX - startX;
      croppedCanvas.height = endY - startY;
      croppedCanvas.getContext('2d').putImageData(croppedImageData, 0, 0);
      const croppedImage = new Image();
      croppedImage.src = croppedCanvas.toDataURL('image/jpeg'); // Adjust the format if needed
      // You can now use croppedImage.src as the cropped image source or save it as needed.
      // For example, you can display it or send it to the server.
    }
  }

  function handleCrop() {
    // Trigger the cropping process by clicking the Crop button
    // This function is optional if you want to provide a separate button for cropping.
    startCrop({
      clientX: 0,
      clientY: 0
    });
    updateCrop({
      clientX: endX,
      clientY: endY
    });
    endCrop();
  }
</script>