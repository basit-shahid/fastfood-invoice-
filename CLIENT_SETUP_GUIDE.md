# 📱 Dr. Shawarma - Android Server Setup Manual

This guide outlines exactly how to set up the **Dr. Shawarma POS system** on an Android device to act as a stable, always-on server for your restaurant.

---

## 🏗️ 1. Hardware & Apps Needed
- **Device:** Any Android phone or tablet (Android 7 or higher).
- **Adapter:** USB-C to Ethernet Adapter (for a stable wired connection).
- **Termux:** Download only from [F-Droid](https://f-droid.org/en/packages/com.termux/).

---

## 📥 2. Initial Setup (Termux)
Open Termux and run these commands to prepare the environment:

```bash
pkg update && pkg upgrade -y
pkg install -y git dos2unix
```

### Clone the Project
```bash
git clone https://github.com/basit-shahid/fastfood-invoice-.git
cd fastfood-invoice-
```

---

## ⚙️ 3. Automatic Installation
We've included a script to handle all the technical installations for you.

1.  **Format the script:**
    ```bash
    dos2unix setup-android.sh
    chmod +x setup-android.sh
    ```
2.  **Run the script:**
    ```bash
    ./setup-android.sh
    ```
    *This will install PHP, Node.js, Composer, and set up your database automatically.*

---

## 🔧 4. Final Configuration
To ensure everything works perfectly (Images, Sessions, and Name), run these manually:

1.  **Fix Branding:**
    ```bash
    nano .env
    ```
    - Change `APP_NAME=Laravel` to `APP_NAME="Dr. Shawarma"`.
    - Change `SESSION_DRIVER=database` to `SESSION_DRIVER=file`.
    - Save (Ctrl+O, Enter) and Exit (Ctrl+X).

2.  **Generate Keys & Links:**
    ```bash
    php artisan key:generate
    php artisan storage:link
    php artisan db:seed
    php artisan config:clear
    ```

---

## 🚀 5. Always-On Automation
You don't want to type commands every time you open the phone.

1.  **Create Auto-Start:**
    ```bash
    nano ~/.bashrc
    ```
2.  **Paste this at the bottom:**
    ```bash
    cd ~/fastfood-invoice-
    php artisan serve --host=0.0.0.0 --port=8000 &
    echo "--- Server started on http://localhost:8000 ---"
    ```
3.  **Prevent Phone Sleep:**
    - Slide down your notification bar -> Click **"Acquire Wake Lock"**.
    - Go to Android Settings -> Termux -> Battery -> Set to **"Unrestricted"**.

---

## 🔗 6. Accessing the POS
- **On other devices (on the same network):** 
  - Find your IP by typing `ifconfig` in Termux.
  - Go to `http://[YOUR_IP]:8000` in Chrome.

---

## 🛒 7. Default Logins
- **Owner:** `owner@fastfood.com` / `password`
- **Manager:** `manager@fastfood.com` / `password`
- **Cashier:** `cashier@fastfood.com` / `password`
