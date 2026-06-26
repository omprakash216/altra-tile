# ULTRA Tile Machine — Setup Guide

## Isko Chalane ke liye Kya Chahiye
- **XAMPP** (ya WAMP/Laragon) — PHP + MySQL server
- **Node.js** — React frontend ke liye

---

## Step 1: Database Import Karo

1. XAMPP start karo (Apache + MySQL)
2. Browser mein jao: `http://localhost/phpmyadmin`
3. New database banao: `ultratech_cms`
4. **Import** tab click karo
5. File select karo: `C:\XAMPP\htdocs\ULTRATECH\backend\db\schema.sql`
6. **Go** press karo ✅

---

## Step 2: Backend Config Set Karo

File: `C:\XAMPP\htdocs\ULTRATECH\backend\api\config.php`

```php
define('DB_HOST', 'localhost');  // same rakhna
define('DB_NAME', 'ultratech_cms'); // same rakhna
define('DB_USER', 'root');      // XAMPP ka default
define('DB_PASS', '');          // XAMPP ka default (blank)
```

---

## Step 3: Backend Files XAMPP mein Copy Karo

`C:\XAMPP\htdocs\ULTRATECH\backend\` folder ko XAMPP ke `htdocs` mein copy karo:

```
C:\xampp\htdocs\ULTRATECH\backend\
```

Ya phir **symlink** banao (recommended):
```powershell
# PowerShell (Admin) mein run karo:
New-Item -ItemType Junction -Path "C:\xampp\htdocs\ULTRATECH" -Target "C:\XAMPP\htdocs\ULTRATECH"
```

---

## Step 4: API URL Update Karo (React)

File: `C:\XAMPP\htdocs\ULTRATECH\src\api.js`

```js
export const API_BASE = 'http://localhost/ULTRATECH/backend/api';
```

---

## Step 5: React Dev Server Chalao

```bash
cd C:\XAMPP\htdocs\ULTRATECH
npm run dev
```

Website: `http://localhost:5173`

---

## Admin Panel Access

URL: `http://localhost/ULTRATECH/backend/admin/`

```
Username: admin
Password: admin123
```

---

## Admin Panel Features

| Page | Kya kar sakte ho |
|------|-----------------|
| 🖥️ Dashboard | Overview, recent inquiries |
| 🎯 Hero Slider | Images change karo, headline edit karo |
| 📦 Products | Products add/edit/delete karo |
| 📰 News | Articles publish/unpublish karo |
| 🏗️ Projects | Project gallery manage karo |
| ⚙️ Services | Service cards edit karo |
| 📊 Stats | Numbers (30+, 120+) change karo |
| 📍 Contact Info | Phone, email, address update karo |
| 📬 Inquiries | Contact form submissions dekhna |
