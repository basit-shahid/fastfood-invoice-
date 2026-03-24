---
description: Resetting the system for a fresh start (Cleaning test data)
---

# System Reset Guide (Fresh Start)

If you have finished testing and want to delete all "test" orders/data to give your client a completely clean system, follow these steps.

> [!CAUTION]
> This will permanently delete ALL orders, transactions, and test staff members you have created. It cannot be undone.

## Option 1: Full Reset (Recommended)
This command deletes everything and then runs your "Seeders" to put back the default accounts (Owner, Manager, Cashier) and initial menu items.

// turbo
```powershell
php artisan migrate:fresh --seed
```

## Option 2: Delete ONLY Orders (Keep Menu & Staff)
If you want to keep the menu items and staff accounts you've set up, but delete all the "Test Orders" in the history:

1. Open your terminal in the project folder.
2. Run these commands:
   ```powershell
   php artisan tinker
   ```
3. Inside the tinker screen (indicated by `>>>`), type these lines one by one:
   ```php
   App\Models\OrderItem::truncate();
   App\Models\Order::truncate();
   exit
   ```

## Option 3: Hard Reset (Start from Scratch)
If you want to wipe the entire database completely (including all users and menu):
// turbo
```powershell
php artisan migrate:fresh
```
*(Note: You will need to register a new account after this!)*

---
### Final Step for Both Options: 
After resetting, you can log in with your default credentials and everything will be at **"0 orders"** and ready for the first real customer! 🚀
