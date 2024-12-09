// Szem sprite képei
const eyeImages = ["images/eye/eye1.png", "images/eye/eye2.png", "images/eye/eye3.png", "images/eye/eye4.png"];
let eyeIndex = 0;

const eyeSprite = document.getElementById("eyeSprite");

// Sprite animáció időzítése
setInterval(() => {
    // Következő kép beállítása
    eyeSprite.style.backgroundImage = `url(${eyeImages[eyeIndex]})`;

    // Index növelése, hogy körbeérjen
    eyeIndex = (eyeIndex + 1) % eyeImages.length;
}, 150); // 150 ms váltás

// Célpozíciók (egér követés)
let targetX = 0;
let targetY = 0;

// Aktuális sprite pozíció (késleltetett mozgáshoz)
let currentX = 0;
let currentY = 0;

// Egér pozíció frissítése
document.addEventListener("mousemove", (event) => {
    targetX = event.clientX - 50; // 50px korrekció, hogy a sprite közepén legyen
    targetY = event.clientY - 50;

    // Kiszámítjuk a szöget a sprite és az egér között
    const deltaX = targetX - currentX;
    const deltaY = targetY - currentY;
    const angle = Math.atan2(deltaY, deltaX) * (180 / Math.PI); // szög kiszámítása
    
    // A sprite elforgatása az egér irányába
    eyeSprite.style.transform = `rotate(${angle}deg)`;
});

// Késleltetett animáció az egér követésére
function animateEye() {
    // Lépésenként közeledés az egér pozíciójához
    currentX += (targetX - currentX) * 0.02; // 2%-os lépés
    currentY += (targetY - currentY) * 0.02;

    eyeSprite.style.left = `${currentX}px`;
    eyeSprite.style.top = `${currentY}px`;

    requestAnimationFrame(animateEye);
}

// Animáció indítása
animateEye();