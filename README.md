<div align="center">

# 🖥️ Laravel Backend — Smart Agri-Advisory Platform

### RBAC · MySQL · Bengali UI — কৃষি সুপারিশ ওয়েব অ্যাপ্লিকেশন

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-26%20Tables-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![RBAC](https://img.shields.io/badge/Auth-RBAC-9C27B0?style=for-the-badge)
![Blade](https://img.shields.io/badge/UI-Bengali%20%F0%9F%87%A7%F0%9F%87%A9-006A4E?style=for-the-badge)

</div>

---

## 📌 এক নজরে

এই ফোল্ডারে আছে **Laravel 11** দিয়ে বানানো পুরো ওয়েব অ্যাপ্লিকেশন — ২৬-টেবিলের normalized ডেটাবেজ স্কিমার উপর ভিত্তি করে তৈরি (দেখুন `docs/Database_Schema.md`)। এখানে RBAC অথেন্টিকেশন, ফার্ম প্রোফাইল, সুপারিশ ম্যানেজমেন্ট, Extension Officer টুলস, Supplier মডিউল এবং Admin প্যানেল — সবকিছুই আছে।

> ⚠️ `vendor/` এবং `composer.lock` অন্তর্ভুক্ত নেই কারণ এই sandbox-এ Packagist অ্যাক্সেস নেই। প্রথমবার লোকালি `composer install` চালাতে হবে।

---

## 🚀 সেটআপ

```bash
cd laravel-backend
composer install
cp .env.example .env
php artisan key:generate

# MySQL-এ agri_advisory নামে একটি ডেটাবেজ তৈরি করুন (অথবা .env এডিট করুন), তারপর:
php artisan migrate --seed

# ../ml-service এ চলমান Flask ML সার্ভিসের সাথে কানেক্ট করুন
# ML_SERVICE_URL ইতিমধ্যে .env.example এ সেট করা আছে

php artisan serve   # ➜ http://localhost:8000
```

---

## 🔑 ডেমো অ্যাকাউন্ট (Seeder থেকে)

<div align="center">

| রোল | ইমেইল | পাসওয়ার্ড | স্ট্যাটাস |
|:---:|---|:---:|:---:|
| 🛡️ **Admin** | `admin@agriadvisory.test` | `password123` | ✅ Active |
| 🌾 **Farmer** | `farmer@agriadvisory.test` | `password123` | ✅ Active |
| 👨‍🌾 **Extension Officer** | `officer@agriadvisory.test` | `password123` | ✅ Active |
| 🚚 **Supplier** | `supplier@agriadvisory.test` | `password123` | ✅ Pre-verified |

</div>

Seeder আরও তৈরি করে দেয়: **৬টি `climate_zones`** এবং **১৫টি `crops`** রেফারেন্স রো — ফসলের নামগুলো `ml-service/generate_data.py`-এর সাথে হুবহু মিলে যায়, ফলে `app/Models/Crop.php`-এর `Crop::findByNameOrCreate()` ML সার্ভিস থেকে আসা প্রতিটি ফসলের নামকে সরাসরি `crops.id`-তে রিজলভ করতে পারে।

---

## 🗂️ Schema → Code ম্যাপ (২৬টি টেবিল)

| গ্রুপ | টেবিলসমূহ | স্ট্যাটাস |
|---|---|:---:|
| 🔐 **Core/Auth** | `users` | ✅ সম্পূর্ণ (RBAC, `EnsureRole` middleware) |
| 📚 **Reference** | `climate_zones`, `crops` | ✅ সম্পূর্ণ + সিডেড; `crop_calendar` মাইগ্রেশন আছে, UI নেই (ঐচ্ছিক) |
| 🌱 **Farmer** | `farm_profiles` (pH/N/P/K এখানেই), `weather_logs` | ✅ সম্পূর্ণ; `weather_logs` সুযোগমতো ব্যবহৃত, এখনো OpenWeather cron নেই |
| 🤖 **ML Output** | `recommendations`, `fertilizer_recommendations`, `price_forecasts`, `disease_detections` | ✅ সম্পূর্ণ — `FarmerController`-এর প্রতিটি ML কল এখানে সেভ হয় |
| 💬 **Feedback** | `recommendation_feedback` | ✅ সম্পূর্ণ |
| 👨‍🌾 **Extension Officer** | `officer_verifications`, `officer_zone_assignments`, `officer_overrides`, `advisory_messages`, `alerts`, `training_sessions`, `training_attendees` | ✅ বেশিরভাগ সম্পূর্ণ; `training_attendees`-এর রেজিস্ট্রেশন UI বাকি |
| 🚚 **Supplier** | `suppliers`, `products`, `orders`, `order_items`, `inquiries`, `demand_forecasts` | ✅ সম্পূর্ণ |
| 🛡️ **Admin** | `admin_logs`, `model_retraining_jobs`, `system_backups`, `analytics_snapshots` | ✅ সম্পূর্ণ — প্রতিটি অ্যাডমিন অ্যাকশন `admin_logs`-এ লগ হয় |

---

## 🛡️ RBAC (Role-Based Access Control)

`users.role` চারটি মান নিতে পারে:

```
farmer  │  extension_officer  │  supplier  │  admin
```

`app/Http/Middleware/EnsureRole.php` এই রোলগুলো এনফোর্স করে (`bootstrap/app.php`-এ `role` নামে alias করা)।

> 🔔 নতুন `extension_officer` / `supplier` সাইনআপগুলো `status = pending` দিয়ে শুরু হয় (একটি `suppliers` রো-ও তৈরি হয়, `verified = false`) — অ্যাডমিনের অনুমোদন লাগে:
> `POST /admin/users/{user}/approve`

---

## 🔌 ML সার্ভিসের সাথে সংযোগ

```
Laravel (FarmerController)
        │
        ▼
app/Services/MlService.php  ──HTTP──▶  ml-service/app.py (Flask)
        │
        ▼
Crop::findByNameOrCreate()  →  crops.id রিজলভ
        │
        ▼
recommendations / fertilizer_recommendations
/ price_forecasts / disease_detections  ← হিস্ট্রি হিসেবে সংরক্ষিত
```

এই ডেটাই পরে Extension Officer-রা review ও override করতে পারেন।

---

## 📁 ফোল্ডার ম্যাপ

```
app/Models/                 28 Eloquent মডেল — প্রতিটি মাইগ্রেশনের জন্য একটি
app/Http/Controllers/       Auth, Farmer, ExtensionOfficer, Supplier, Admin
app/Http/Middleware/        EnsureRole.php  (RBAC)
app/Services/MlService.php  Flask ML মাইক্রোসার্ভিসের জন্য HTTP ক্লায়েন্ট
database/migrations/        26 টেবিলের জন্য 28টি মাইগ্রেশন ফাইল, FK-safe অর্ডারে
database/seeders/           DatabaseSeeder.php — zones, crops, ডেমো ইউজার
routes/web.php               সব রুট, রোল অনুযায়ী গ্রুপড
resources/views/             Blade টেমপ্লেট (বাংলা UI), রোল অনুযায়ী
public/css/app.css           স্টাইলিং
public/js/app.js             Axios হেল্পার ফাংশন
```

---

<div align="center">

🔗 **Live App:** [agriadvisory.app](http://agriadvisory.app)  ·  📦 **Repo:** [Agri_Advisory](https://github.com/Marjan15H/Agri_Advisory)

Made with 💚 for Bangladeshi Farmers | CSE347 Project

</div>
