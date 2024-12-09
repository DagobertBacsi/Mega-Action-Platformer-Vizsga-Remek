const spriteImages = [
    "images/player/playerwalk1.png",
    "images/player/playerwalk2.png",
    "images/player/playerwalk3.png",
    "images/player/playerwalk4.png",
    "images/player/playerwalk5.png",
    "images/player/playerwalk6.png"
];

const spriteElement = document.getElementById('sprite');

let currentImageIndex = 0;

function animateSprite() {
    spriteElement.style.backgroundImage = `url(${spriteImages[currentImageIndex]})`;

    currentImageIndex = (currentImageIndex + 1) % spriteImages.length;
}

setInterval(animateSprite, 150);