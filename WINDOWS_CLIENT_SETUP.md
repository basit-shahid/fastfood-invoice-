# 🖥️ Dr. Shawarma - Windows Server Local Setup Manual

This guide outlines exactly how a client can set up the **Dr. Shawarma POS system** on a Windows computer to run locally as a server for the restaurant.

---

## 🏗️ 1. Prerequisites (Install Once)

Before starting, the client needs to install the following software on their Windows machine:
1. **PHP (>= 8.2)**: [Download PHP](https://windows.php.net/download/)
    * Extract to `C:\php`
    * Add `C:\php` to your System Environment Variables (PATH).
    * Copy `php.ini-development` to `php.ini` and enable necessary extensions (`extension=pdo_sqlite`, `extension=fileinfo`, `extension=mbstring`, `extension=openssl`, `extension=curl`).
2. **Composer**: [Download Composer](https://getcomposer.org/Composer-Setup.exe) (Install it normally, pointing to `C:\php\php.exe`).
3. **Node.js**: [Download Node.js](https://nodejs.org/) (Install it normally).
4. **Git**: [Download Git](https://git-scm.com/download/win) (Avoid downloading ZIP manually).

---

## 📥 2. Initial Setup

Open **Command Prompt** or **PowerShell** as Administrator, then run these commands:

### Clone the Project
```cmd
cd /d C:\
git clone -b local-setup https://github.com/basit-shahid/fastfood-invoice-.git
cd fastfood-invoice-
```

*(Note: We are using the `local-setup` branch specifically designed for local usage without online deployment clutter).*

---

## ⚙️ 3. Installation & Configuration

Now, run the following commands sequentially to set up the software dependencies and database:

1. **Install PHP Packages:**
   ```cmd
   composer install
   ```

2. **Install Node/NPM Packages:**
   ```cmd
   npm install
   npm run build
   ```

3. **Environment Setup:**
   ```cmd
   copy .env.example .env
   ```

4. **Generate App Key and Storage Link:**
   ```cmd
   php artisan key:generate
   php artisan storage:link
   ```

5. **Setup Database (SQLite):**
   ```cmd
   type NUL > database\database.sqlite
   php artisan migrate --seed
   ```
   *(This creates an empty local SQLite DB and seeds it with default users/menu).*

---

## 🚀 4. Running the Local Server

Whenever you want to start the POS system, open Command Prompt, navigate to the folder, and start the local server:

```cmd
cd /d C:\fastfood-invoice-
php artisan serve --host=0.0.0.0 --port=8000
```

*Leave this window running. Closing it will shut down the server.*

---

## 🔗 5. Accessing the POS

### On the Server Computer Itself:
- Open Chrome and visit: `http://localhost:8000`

### On Other Devices (Same Wifi / LAN):
If you want cashiers to access it via tablets or other PCs:
1. Open Command Prompt on the Server PC and type: `ipconfig`
2. Find the **IPv4 Address** (e.g., `192.168.1.10`)
3. On the cashier tablet, go to Chrome and enter: `http://192.168.1.10:8000`
4. *(If it doesn't open, ensure the Windows Firewall on the Server PC allows traffic through port 8000).*

---

## 🛒 6. Default Logins
- **Owner:** `owner@fastfood.com` / `password`
- **Manager:** `manager@fastfood.com` / `password`
- **Cashier:** `cashier@fastfood.com` / `password`
