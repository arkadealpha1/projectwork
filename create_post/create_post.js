document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");

    form.addEventListener("submit", function (event) {
        const productName = document.getElementById("product_name").value.trim();
        const description = document.getElementById("description").value.trim();
        const price = document.getElementById("price").value.trim();
        const title = document.getElementById("title").value.trim();
        const reviewBlog = document.getElementById("review_blog").value.trim();
        
        if (!productName || !description || !price || !title || !reviewBlog) {
            alert("Please fill in all required fields.");
            event.preventDefault();
        }
    });

    document.getElementById("image").addEventListener("change", function (event) {
        previewImage(event, "image-preview");
    });
    
    document.getElementById("media").addEventListener("change", function (event) {
        previewImage(event, "media-preview");
    });
});

function previewImage(event, previewId) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            let previewElement = document.getElementById(previewId);
            if (!previewElement) {
                previewElement = document.createElement("img");
                previewElement.id = previewId;
                event.target.parentNode.appendChild(previewElement);
            }
            previewElement.src = e.target.result;
            previewElement.style.maxWidth = "100px";
            previewElement.style.marginTop = "10px";
        };
        reader.readAsDataURL(file);
    }
}
