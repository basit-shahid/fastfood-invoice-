---
description: Deploying the POS system locally for multiple devices (LAN)
---

# Local Network Deployment Guide

Follow these steps to run the POS system on 2-3 devices (e.g., a manager PC, a cashier PC, and a tablet) using only your local Wi-Fi or router—**no internet or extra costs required.**

## 1. Designate a "Primary Server"
Choose the best computer among your devices to be the **Host**. This computer must stay on and keep the software running for others to use it.

## 2. Connect All Devices to the Same Router
Ensure all devices (PCs, phones, or tablets) are connected to the **same Wi-Fi** or the same physical router (Ethernet cables). 

> [!NOTE]
> You do **not** need an active internet connection on this router. It only needs to connect the devices to each other.

## 3. Find your Server's IP Address
On the **Host Computer**:
1. Open the search bar and type `cmd`, then press Enter.
2. In the black window, type `ipconfig` and press Enter.
3. Look for **IPv4 Address**. It usually looks like `192.168.1.XX` or `10.0.0.XX`.
4. **Write this number down.**

## 4. Launch the System for the Network
On the **Host Computer**, open your terminal in the `fastfood-invoice-system` folder and run this specific command:

// turbo
```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

> [!IMPORTANT]
> The `--host=0.0.0.0` part is what allows other devices to see the website. Without it, only the main PC can see it.

## 5. Access from Other Devices
On your other devices (Cashier PC or tablet):
1. Open any web browser (Chrome, Edge, Safari).
2. In the address bar, type your Server's IP address followed by `:8000`.
   - *Example:* if your IP was `192.168.1.15`, type `http://192.168.1.15:8000`
3. Press Enter, and the login page will appear!

## 6. (Optional) Quick Access
To make it easier for daily use, you can **"Add to Home Screen"** on a tablet or **"Create Shortcut"** on a desktop for that URL so staff don't have to type the IP address every time.
