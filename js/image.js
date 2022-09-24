// TODO: remove duplication between here and contact.js
var profileImage = document.getElementById("profilePicture");
profileImage.onclick = function() {
  let upload = document.getElementById("fileUpload");
  console.log(upload);
  upload.click();
}

// // TODO: think of how to pass this to doCreateContact in a better way
// let imgAsBase64String = "";

window.addEventListener('load', function() {
  document.querySelector('input[type="file"]').addEventListener('change', function() {
    if (this.files && this.files[0]) {
      var reader = new FileReader();

      reader.onload = function () {
        imgAsBase64String = reader.result.replace("data:", "")
        .replace(/^.+,/, "");
        localStorage.setItem("imgAsBase64String", imgAsBase64String);
      }
      reader.readAsDataURL(this.files[0]);

      profileImage.src = URL.createObjectURL(this.files[0]); // set src to blob url
    }
  });
});
