# 📱 Running as a Server on Android

This guide will show you how to turn your Android device into the primary server for the **Dr. Shawarma** POS system. This allows you to carry your entire billing system in your pocket!

## 1. Install Termux
Do **not** use the Play Store version of Termux (it is outdated). 
1. Download **Termux** from [F-Droid](https://f-droid.org/en/packages/com.termux/).
2. Open Termux on your phone.

## 2. Transfer Project Files
You need to get the project files onto your phone. You can:
- **Zip and Transfer:** Zip the project on your PC, send it to your phone, and extract it in Termux using `termux-setup-storage`.
- **Use Git:** If your project is in a repository, run `pkg install git` and `git clone [your-repo-url]`.

## 3. Run the Auto-Setup Script
Once you are inside the project folder in Termux, run:

```bash
chmod +x setup-android.sh
./setup-android.sh
```

This script will automatically:
- Install PHP, Node.js, and Composer.
- Set up your `.env` for SQLite.
- Install all necessary dependencies.

## 4. Start the Server
Once the setup is finished, start the server with:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## 5. Accessing the System
- **On the same Android Phone:** Open Chrome and go to `http://localhost:8000`.
- **From Other Devices (on the same Wi-Fi):**
  1. In Termux, type `ifconfig` and look for your IP address (usually under `wlan0`, starts with `192.168.x.x`).
  2. On another device, go to `http://[YOUR_IP]:8000`.

> [!TIP]
> Keep Termux running in the background. If you close Termux, the server will stop!
