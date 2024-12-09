# 🌐 **Mega Action Platformer - Weboldal + Launcher**  
[![PHP](https://img.shields.io/badge/PHP-%5E8.0-blue)](https://www.php.net/)  
[![CSS](https://img.shields.io/badge/CSS-SASS%2FSCSS-ff69b4)](https://sass-lang.com/)  

Üdvözöllek a **Mega Action Platformer** projektben! 🎉 Ez a projekt egy teljes értékű weboldal, amely tartalmaz regisztrációs és bejelentkezési rendszert, adminisztrációs panelt, valamint egy játékindító (launcher) rendszert automatikus frissítésekkel. 🚀  

---

## 🛠 **Technológiák**  

### Backend  
- **PHP 8.0+**: Erőteljes és gyors szerveroldali nyelv.  
- **MySQL**: Adataid biztonságos tárolása egy strukturált adatbázisban.  

### Frontend  
- **HTML5**: A modern és reszponzív weboldal alapja.  
- **CSS (SASS)**: Stílusos és könnyen karbantartható megjelenés.  
- **JavaScript**: Interaktív elemek és dinamikus funkciók.  

### Extra funkciók  
- **Admin Panel**: Kezeld az érméket és felhasználókat egyszerűen.  
- **Launcher**: A játékindító automatikus frissítési funkcióval.  

---

## 📂 **Projektstruktúra**  

```plaintext
MAP Website/
├── images/               # Képek tárolása
├── assets/               # Statikus fájlok
│   ├── css/              # Stíluslapok
│   ├── js/               # JavaScript fájlok
│   ├── sass/             # SASS forráskódok
│   └── webfont/          # Webes betűkészletek
├── php/                  # PHP fájlok
│   ├── scoreboard.php    # Scoreboard funkció
│   ├── register.php      # Regisztráció logika
│   ├── login.php         # Bejelentkezés logika
│   ├── admin.php         # Admin panel
│   ├── register.css      # Regisztráció stíluslap
│   ├── login.css         # Bejelentkezés stíluslap
│   ├── admin.css         # Admin panel stíluslap
│   └── scoreboard.css    # Scoreboard stíluslap
├── index.html            # Kezdőlap
└── README.md             # Dokumentáció
```

## 📋 **Telepítés**  

### 1️⃣ Előfeltételek  

#### Weboldalhoz  
- **XAMPP**: PHP, MySQL és Apache webszerver egyszerűen telepíthető környezetben. [Töltsd le itt](https://www.apachefriends.org/index.html).  
- Egy programozó szoftver, például **Visual Studio Code** vagy **PhpStorm**.  

#### Launcherhez  
- **.NET Core 3.1**: A játékindító fejlesztéséhez szükséges keretrendszer. [Töltsd le itt](https://dotnet.microsoft.com/download/dotnet/3.1).  
- A **Visual Studio 2019+** programozó szoftver.  

---

### 2️⃣ Weboldal telepítése  

1. **XAMPP beállítása**:  
   - Telepítsd a XAMPP-ot, és indítsd el az Apache és MySQL modulokat.  
2. **Projekt másolása**:  
   - Másold a `MAP Website` mappát a XAMPP `htdocs` könyvtárába (pl. `C:/xampp/htdocs/MAP Website/`).  
3. **Adatbázis importálása**:  
   - Nyisd meg a böngésződben a `http://localhost/phpmyadmin` oldalt.  
   - Hozz létre egy új adatbázist (`db_mega`), vagy másold ki a kódot lejjebb.  
4. **Weboldal elérése**:  
   - Lépj a böngésződben a `http://localhost/MAP Website/index.html` címre.

---
     
### 3️⃣ Launcher telepítése

1. **.NET Core környezet beállítása**:  
   - Telepítsd a .NET Core 3.1 runtime-ot és SDK-t.    
2. **Projekt megnyitása**:  
   - Nyisd meg a `GameLauncher.sln` fájlt.  
3. **Launcher futtatása**:  
   - Indítsd el a programot és az autómatikusan letölti neked a játékot.

### 🗄 **Adatbázis létrehozása**  

Használj MySQL-t vagy phpMyAdmin-t az alábbi SQL script futtatásához:  

```sql
-- Adatbázis létrehozása
CREATE DATABASE IF NOT EXISTS db_mega;

-- Adatbázis használata
USE db_mega;

-- Tábla létrehozása
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,      -- Egyedi azonosító, automatikusan növekvő
    username VARCHAR(50) NOT NULL,          -- Felhasználónév, maximum 50 karakter
    pass VARCHAR(255) NOT NULL,             -- Jelszó, titkosítva tárolva
    coin INT DEFAULT 0,                     -- Felhasználóhoz tartozó "coin", alapértelmezetten 0
    email VARCHAR(100) UNIQUE NOT NULL      -- E-mail cím, maximum 100 karakter, egyedi érték
);
```

---

### 📸 **Képernyőképek**  

| **Weboldal**         | **Admin Panel**      | **Launcher**         |
|-----------------------|----------------------|----------------------|
| ![Weboldal Screenshot](MAP%20Website/images/screenshots/webpage.png) | ![Admin Panel Screenshot](MAP%20Website/images/screenshots/admin-panel.png) | ![Launcher Screenshot](MAP%20Website/images/screenshots/launcher.png) |

---

## 🚀 **Főbb funkciók**  

### 🌟 **Felhasználói modulok**  
- **Regisztráció**: Gyors és biztonságos.  
- **Bejelentkezés**: Gyors és biztonságos bejelentkezés.  

### 🔒 **Admin Panel**  
- **Felhasználókezelés**: Felhasználók listázása, törlése, érmék módosítása.    

### 📊 **Scoreboard**  
- **Élő eredménytábla**: Játékosok és érmék megjelenítése.  
