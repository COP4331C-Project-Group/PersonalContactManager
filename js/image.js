var resizeImage = function (settings) {
  var file = settings.file;
  var maxSize = settings.maxSize;
  var reader = new FileReader();
  var image = new Image();
  var canvas = document.createElement('canvas');
  var dataURItoBlob = function (dataURI) {
    var bytes = dataURI.split(',')[0].indexOf('base64') >= 0 ?
    atob(dataURI.split(',')[1]) :
    unescape(dataURI.split(',')[1]);
    var mime = dataURI.split(',')[0].split(':')[1].split(';')[0];
    var max = bytes.length;
    var ia = new Uint8Array(max);
    for (var i = 0; i < max; i++)
      ia[i] = bytes.charCodeAt(i);
    return new Blob([ia], { type: mime });
  };
  var resize = function () {
    var width = image.width;
    var height = image.height;
    if (width > height) {
      if (width > maxSize) {
        height *= maxSize / width;
        width = maxSize;
      }
    } else {
      if (height > maxSize) {
        width *= maxSize / height;
        height = maxSize;
      }
    }
    canvas.width = width;
    canvas.height = height;
    canvas.getContext('2d').drawImage(image, 0, 0, width, height);
    var dataUrl = canvas.toDataURL('image/jpeg');
    return dataURItoBlob(dataUrl);
  };
  return new Promise(function (ok, no) {
    if (!file.type.match(/image.*/)) {
      no(new Error("Not an image"));
      return;
    }
    reader.onload = function (readerEvent) {
      image.onload = function () { return ok(resize()); };
      image.src = readerEvent.target.result;
    };
    reader.readAsDataURL(file);
  });
};

function blobToBase64(blob) {
  return new Promise((resolve, _) => {
    const reader = new FileReader();
    reader.onloadend = () => resolve(reader.result);
    reader.readAsDataURL(blob);
  });
}

var profileImage = document.getElementById("profilePicture");
profileImage.onclick = function() {
  let upload = document.getElementById("fileUpload");
  upload.click();
}

window.addEventListener('load', function() {
  document.querySelector('input[type="file"]').addEventListener('change', async function() {
    if (this.files && this.files[0]) {
      if (!this.files[0].type.match(/image.*/)) {
        no(new Error("Not an image"));
        return;
      }
      const config = {
        file: this.files[0],
        maxSize: 500
      };
      const resizedImage = await resizeImage(config);

      // Don't upload files greater than file size limit
      if (resizedImage.size > window.imageSizeLimit) {
        imgAsBase64String = "";
        alert("something bad happened, img resizing failed");
      } else {
        imgAsBase64String = await blobToBase64(resizedImage);
        imgAsBase64String = imgAsBase64String.replace("data:", "")
        .replace(/^.+,/, "");
        // set src to blob url
        profileImage.src = URL.createObjectURL(resizedImage);
      }
      localStorage.setItem('uploadedImgAsBase64String', imgAsBase64String);
    }
  });
});
