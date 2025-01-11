document.getElementById("grid-layout").style.display = "block";
document.getElementById("carousel-layout").style.display = "none";

document.getElementById("style1").onclick = function() {
    document.getElementById("grid-layout").style.display = "block";
    document.getElementById("carousel-layout").style.display = "none";
};

document.getElementById("style2").onclick = function() {
    document.getElementById("grid-layout").style.display = "none";
    document.getElementById("carousel-layout").style.display = "block";
    showSlides(slideIndex); // Ensure the carousel is initialized
};

let slideIndex = 1;
function plusSlides(n) {
    showSlides(slideIndex += n);
}

function showSlides(n) {
    let slides = document.getElementsByClassName("carousel-img");
    let totalSlides = slides.length;

    // Ensure slideIndex stays within bounds
    if (n > totalSlides - 3) { slideIndex = totalSlides - 3; }
    if (n < 1) { slideIndex = 1; }

    // Hide all slides
    for (let i = 0; i < totalSlides; i++) {
        slides[i].style.display = "none";
        slides[i].classList.remove("middle", "side");
    }

    // Show the current set of 3 images and apply scaling
    for (let i = slideIndex - 1; i < slideIndex + 2; i++) {
        if (slides[i]) {
            slides[i].style.display = "block";
        }
    }

    // Apply scaling
    let middleIndex = slideIndex; // The middle image index
    let sideIndices = [slideIndex - 1, slideIndex + 1]; 

    if (slides[middleIndex]) {
        slides[middleIndex].classList.add("middle");
    }
    for (let i of sideIndices) {
        if (slides[i]) {
            slides[i].classList.add("side");
        }
    }
}