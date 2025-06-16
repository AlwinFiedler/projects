const sidebar = document.getElementById('nav-bar');

sidebar.addEventListener('mouseenter', () => {
  sidebar.style.left = '0';
});

sidebar.addEventListener('mouseleave', () => {
  sidebar.style.left = '-280px';
});

const footerBar = document.getElementById('footer-bar');

footerBar.addEventListener('mouseenter', () => {
  footerBar.style.right = '0';
});

footerBar.addEventListener('mouseleave', () => { 
  footerBar.style.right = '-280px';
});

const modal = document.getElementById("imageModal");
const fullImage = document.getElementById("fullImage");
const closeBtn = document.querySelector(".close");

document.querySelectorAll(".slide-image").forEach((img) => {
    img.addEventListener("click", () => {
        modal.style.display = "block";
        fullImage.src = img.src;
    });
});

closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
});

modal.addEventListener("click", (e) => {
    if (e.target === modal) {
        modal.style.display = "none";
    }
});