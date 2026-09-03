
document.addEventListener('DOMContentLoaded', () => {
// =========================
// 🔥 SLIDER PRO (SEGURO)
// =========================

const slides = document.querySelectorAll('.slide');
const next = document.querySelector('.next');
const prev = document.querySelector('.prev');
const dots = document.querySelectorAll('.dot');

let index = 0;
let interval;

function showSlide(i) {
    if (!slides.length || !dots.length) return;

    slides.forEach(s => s.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));

    if (slides[i]) slides[i].classList.add('active');
    if (dots[i]) dots[i].classList.add('active');
}

function nextSlide() {
    if (!slides.length) return;
    index = (index + 1) % slides.length;
    showSlide(index);
}

function prevSlide() {
    if (!slides.length) return;
    index = (index - 1 + slides.length) % slides.length;
    showSlide(index);
}

function startSlider() {
    if (!slides.length) return;
    interval = setInterval(nextSlide, 4000);
}

function stopSlider() {
    clearInterval(interval);
}

if (slides.length > 0 && next && prev) {

    next.addEventListener('click', () => {
        nextSlide();
        stopSlider();
        startSlider();
    });

    prev.addEventListener('click', () => {
        prevSlide();
        stopSlider();
        startSlider();
    });

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            index = i;
            showSlide(index);
            stopSlider();
            startSlider();
        });
    });

    startSlider();
}


});