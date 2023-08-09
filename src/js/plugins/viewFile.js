function previewFile(event) {
  const fileInput = event.target;
  const file = fileInput.files[0];

  if (file) {
    const fileReader = new FileReader();

    fileReader.onload = function () {
      const imagePreview1 = document.getElementById('imagePreview1');
      imagePreview1.src = fileReader.result;
    };

    fileReader.readAsDataURL(file);
  }
}
