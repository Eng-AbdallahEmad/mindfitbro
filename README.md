# MindFitBro — منصة اللياقة البدنية الذكية

<p align="center">
  <img src="public/assets/logo/mindfitbro.png" alt="MindFitBro Logo" width="200"/>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white"/>
  <img src="https://img.shields.io/badge/Vite-7.x-646CFF?style=for-the-badge&logo=vite&logoColor=white"/>
</p>

---

## نظرة عامة

**MindFitBro** منصة ويب متكاملة للياقة البدنية والتدريب الشخصي، مبنية بـ Laravel 12، توصل العملاء بالكوتشات المتخصصين وتساعدهم على تتبع تقدمهم اليومي.

المنصة تدعم **3 أدوار مختلفة**:

| الدور | الوصف |
|-------|-------|
| 👤 **User** | العميل — يشتري الباقة، يحجز المقابلة، يتابع تقدمه |
| 🏋️ **Coach** | الكوتش — يدير المقابلات، يتابع المشتركين، يسجل التقييمات |
| ⚙️ **Admin** | المشرف — إدارة كاملة للمنصة |

---

## المميزات الرئيسية

### للعملاء
- **شراء بدون تسجيل (Guest Checkout)** — اشتري مباشرةً بالاسم والإيميل دون الحاجة لإنشاء حساب مسبق
- **سلة مشتريات ذكية** — إضافة وتعديل وحذف الباقات، دعم الأسعار السنوية والكوبونات
- **حجز مواعيد المقابلات** — اختيار وقت مناسب مع الكوتش مباشرةً من الداشبورد
- **تتبع التقدم** — متابعة الوزن، الحضور، اكتمال البرنامج التدريبي
- **حساب السعرات الحرارية** — أداة مدمجة لحساب احتياجات السعرات

### للكوتشات
- **داشبورد احترافي** — إحصائيات شاملة: عدد العملاء، الجلسات، والإيرادات الشهرية
- **إدارة المقابلات** — قبول أو رفض المواعيد وإضافة روابط Google Meet
- **متابعة المشتركين** — عرض تفصيلي لكل عميل مع خريطة حضور الـ 90 يوم
- **تسجيل التقييمات** — تسجيل بيانات اللياقة (وزن، نسبة دهون، كتلة عضلية)
- **تسجيل الحضور** — تتبع يومي للحضور (حضر / تأخر / غاب)

### عام
- **دعم ثنائي اللغة (AR/EN)** — واجهة كاملة بالعربية والإنجليزية
- **إيميلات تلقائية** — تأكيد الشراء مع الفاتورة، استعادة كلمة المرور
- **تصميم متجاوب** — يعمل على جميع الأجهزة (موبايل، تابلت، كمبيوتر)

---

## التقنيات المستخدمة

### Backend
| التقنية | الإصدار | الغرض |
|---------|---------|--------|
| PHP | ^8.2 | لغة البرمجة الأساسية |
| Laravel | 12.0 | إطار عمل الويب |
| MySQL | latest | قاعدة البيانات |

### Frontend
| التقنية | الإصدار | الغرض |
|---------|---------|--------|
| Tailwind CSS | 3.x | تصميم الواجهات |
| Alpine.js | CDN | التفاعلية والـ reactivity |
| Vite | 7.x | بناء الأصول |
| Swiper.js | 11.x | الـ sliders |
| GSAP | 3.12 | الأنيميشن |
| Material Symbols | Google | الأيقونات |
| Cairo Font | Google | الخط العربي |

---

## متطلبات التشغيل

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL >= 8.0
- XAMPP / Laragon / أي خادم محلي

---

## طريقة التثبيت

### 1. استنساخ المشروع
```bash
git clone https://github.com/Eng-AbdallahEmad/mindfitbro.git
cd mindfitbro
```

### 2. تثبيت الحزم
```bash
composer install
npm install
```

### 3. إعداد ملف البيئة
```bash
cp .env.example .env
php artisan key:generate
```

### 4. إعداد قاعدة البيانات
في ملف `.env` عدّل البيانات التالية:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mindfitbro_db
DB_USERNAME=root
DB_PASSWORD=
```

### 5. إعداد البريد الإلكتروني
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.spacemail.com
MAIL_PORT=465
MAIL_USERNAME=info@mindfitbro.com
MAIL_PASSWORD=your_password_here
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=info@mindfitbro.com
MAIL_FROM_NAME="MindFitBro"
```

### 6. تشغيل الـ Migrations والـ Seeders
```bash
php artisan migrate --seed
```

### 7. بناء الأصول
```bash
npm run build
# أو في وضع التطوير:
npm run dev
```

### 8. تشغيل المشروع
```bash
php artisan serve
```
ثم افتح المتصفح على: `http://localhost:8000`

---

## هيكل قاعدة البيانات

```
users                    ← المستخدمون (عملاء / كوتشات / أدمن)
user_profiles            ← بيانات اللياقة (طول، وزن، تاريخ ميلاد)
plans                    ← الباقات المتاحة
features                 ← المميزات المتاحة
feature_plan             ← pivot: ربط الباقات بالمميزات
subscriptions            ← اشتراكات العملاء (+ بيانات الـ guest checkout)
carts                    ← سلال المشتريات
cart_items               ← عناصر السلة
meeting_bookings         ← مواعيد المقابلات
programs                 ← البرامج التدريبية
program_days             ← أيام البرنامج
user_workout_logs        ← سجلات التمارين
weight_logs              ← سجلات الوزن
attendances              ← سجلات الحضور
member_evaluations       ← تقييمات اللياقة من الكوتش
```

---

## الباقات المتاحة

| الباقة | الاسم | السعر الشهري | الخصم السنوي | الأكثر طلباً |
|--------|-------|-------------|------------|------------|
| 🥉 Starter | الأساسي | 299 ر.س | 10% | — |
| 🥇 Pro | النخبة | 599 ر.س | 15% | ✅ |
| 💎 Elite | إيليت | 999 ر.س | 20% | — |

### كوبونات الخصم المتاحة (10%)
```
MFB10 · MINDFITBRO · WELCOME · EID2025
```

---

## هيكل المشروع

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/AuthController.php          ← تسجيل، دخول، استعادة كلمة مرور
│   │   └── Web/
│   │       ├── HomeController.php           ← الصفحة الرئيسية
│   │       ├── CartController.php           ← إدارة السلة (AJAX)
│   │       ├── CheckoutController.php       ← الدفع والـ Guest Checkout
│   │       ├── DashboardController.php      ← داشبورد العميل والكوتش
│   │       ├── BookingController.php        ← حجز المواعيد
│   │       └── SubscriberController.php     ← متابعة المشتركين
│   └── Middleware/
├── Mail/
│   └── PurchaseConfirmationMail.php         ← إيميل تأكيد الشراء
├── Models/
│   ├── User.php · Subscription.php · Plan.php
│   ├── Cart.php · CartItem.php · Feature.php
│   ├── MeetingBooking.php · Program.php
│   ├── Attendance.php · MemberEvaluation.php
│   └── WeightLog.php · UserWorkoutLog.php
├── Notifications/
│   └── ResetPasswordNotification.php        ← إشعار استعادة كلمة المرور
└── Services/Web/
    ├── HomeService.php                      ← بيانات الصفحة الرئيسية
    ├── CartService.php                      ← منطق السلة والأسعار
    ├── CheckoutService.php                  ← منطق الدفع
    ├── DashboardService.php                 ← بيانات داشبورد العميل
    └── CoachDashboardService.php            ← بيانات داشبورد الكوتش

resources/
├── views/
│   ├── layouts/web/app.blade.php           ← اللايوت الرئيسي
│   ├── components/web/                     ← navbar, sidebar, footer, ...
│   ├── auth/web/                           ← login, register, complete_account, ...
│   ├── app/web/                            ← home, cart, dashboard, bookings, ...
│   └── mail/                              ← قوالب الإيميلات
├── lang/
│   ├── ar/messages.php                     ← الترجمة العربية
│   └── en/messages.php                     ← الترجمة الإنجليزية
└── css/ · js/

database/
├── migrations/                             ← 24+ migration file
└── seeders/
    ├── PlanSeeder.php                      ← 3 plans + 9 features
    └── CoachSeeder.php                     ← حساب الكوتش التجريبي
```

---

## سير العمل الرئيسي

### 1. رحلة العميل العادي
```
التسجيل ← اختيار الباقة ← السلة ← الدفع ← حجز موعد المقابلة ← متابعة التقدم
```

### 2. رحلة الـ Guest Checkout
```
اختيار الباقة ← السلة ← الدفع بالاسم والإيميل
    ← إيميل تأكيد + فاتورة
    ← رابط إكمال الحساب
    ← إنشاء حساب + ربطه بالباقة
    ← حجز موعد المقابلة
```

### 3. دور الكوتش
```
استقبال طلبات المقابلات ← قبول/رفض + إضافة رابط Meet
    ← تسجيل الحضور اليومي
    ← تسجيل التقييمات الدورية
    ← متابعة تقدم كل عميل
```

---

## الـ Routes الرئيسية

| المسار | الطريقة | الوظيفة |
|--------|---------|---------|
| `/` | GET | الصفحة الرئيسية |
| `/auth/register` | GET/POST | تسجيل حساب جديد |
| `/auth/login` | GET/POST | تسجيل الدخول |
| `/auth/forgot-password` | GET/POST | استعادة كلمة المرور |
| `/cart` | GET | عرض السلة |
| `/cart/add` | POST | إضافة باقة للسلة |
| `/cart/apply-coupon` | POST | تطبيق كوبون خصم |
| `/checkout` | POST | إتمام الشراء |
| `/checkout/success/{id}` | GET | صفحة نجاح الشراء |
| `/complete-account/{token}` | GET/POST | إكمال بيانات حساب الـ Guest |
| `/dashboard` | GET | الداشبورد (عميل أو كوتش) |
| `/schedule-meeting/{subscription}` | GET | حجز موعد مقابلة |
| `/coach/bookings` | GET | قائمة مواعيد الكوتش |
| `/coach/subscribers` | GET | قائمة مشتركي الكوتش |
| `/coach/subscribers/{id}` | GET | ملف المشترك التفصيلي |
| `/calorie-calculator` | GET | حاسبة السعرات |

---

## المتغيرات البيئية المهمة

```env
APP_NAME=MindFitBro
APP_ENV=local
APP_URL=http://localhost

DB_DATABASE=mindfitbro_db

MAIL_MAILER=smtp
MAIL_HOST=mail.spacemail.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
MAIL_USERNAME=info@mindfitbro.com
MAIL_PASSWORD=          ← مطلوب تعبئته

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

---

## حسابات تجريبية (بعد تشغيل الـ Seeders)

| الدور | اسم المستخدم | كلمة المرور |
|-------|-------------|-------------|
| Coach | coach@salim | (من CoachSeeder) |

---

## التطوير المستقبلي

- [ ] لوحة تحكم الأدمن الكاملة
- [ ] نظام الإشعارات الفورية
- [ ] تطبيق موبايل
- [ ] بوابة دفع إلكترونية (Stripe / Moyasar)
- [ ] تقارير وإحصائيات متقدمة

---

## المطور

**Abdallah Emad**
- GitHub: [@Eng-AbdallahEmad](https://github.com/Eng-AbdallahEmad)

---

<p align="center">
  صُنع بـ ❤️ لمساعدة الناس على الوصول لأفضل نسخة من أنفسهم
</p>
