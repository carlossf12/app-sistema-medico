function previewImage(event) {
  var image = document.getElementById('imagePreview');
  var input = event.target;
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      image.src = e.target.result;
      image.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  } else {
    image.style.display = 'none';
  }
}
