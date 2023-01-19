let slideIndex = 0;
let timeoutHandle;
let slides = document.getElementsByClassName("mySlides");
let dots = document.getElementsByClassName("dot");

if (slides.length > 0) {
    setSlide();
}

function setSlide(n = slideIndex) {
    window.clearTimeout(timeoutHandle);
    slideIndex = n;
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = i === slideIndex ? "block" : "none";
    }
    for (let i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace("isActive", "");
    }
    dots[slideIndex].className += " isActive";
    dots[slideIndex + dots.length / slides.length].className += " isActive";
    slideIndex = (slideIndex + 1) % slides.length;
    timeoutHandle = window.setTimeout(setSlide, 4000);
}
