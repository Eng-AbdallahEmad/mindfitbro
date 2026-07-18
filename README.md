# MindFitBro — Smart Fitness Platform

<p align="center">
  <img src="public/assets/logo/mindfitbro.png" alt="MindFitBro Logo" width="200"/>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Version-1.1.0-D4ED57?style=for-the-badge"/>
  <img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white"/>
</p>

---

## Overview

**MindFitBro** is a full-featured fitness and personal training web platform built with Laravel 12. It connects clients with specialized coaches, manages subscriptions end-to-end, and gives administrators full control over every aspect of the platform.

The platform supports **3 distinct roles**:

| Role | Description |
|------|-------------|
| 👤 **Client** | Purchases a plan, books sessions, tracks progress, earns referral rewards |
| 🏋️ **Coach** | Manages bookings, monitors subscribers, records evaluations and attendance |
| ⚙️ **Admin** | Full platform control — users, plans, subscriptions, settings, content, maintenance |

---

## Key Features

### 👤 For Clients
- **Guest Checkout** — Purchase directly with name and email, no account required upfront
- **Multi-Currency Pricing** — Prices shown in the visitor's local currency (SAR / EGP / TND / USD) via IP detection
- **Flexible Duration** — Choose 3-month or 6-month subscription periods with per-duration pricing
- **Coupon Discounts** — Apply discount coupons at checkout
- **Session Booking** — Schedule online coaching sessions with date/time picker from the dashboard
- **Progress Tracking** — Weight logs, attendance history, and training program completion
- **Journey PDF** — Download a printable PDF summary of the fitness journey
- **Family Reward** — Invite a friend and earn a discount on the next renewal
- **Calorie Calculator** — Built-in tool to calculate daily calorie needs
- **Profile Completion** — After approval, guest accounts receive an email to set their password and complete their profile

### 🏋️ For Coaches
- **Professional Dashboard** — Total clients, upcoming sessions, and monthly revenue
- **Booking Management** — Accept or reject appointments; add Google Meet links
- **Subscriber Monitoring** — Per-client view with a 90-day attendance heatmap
- **Fitness Evaluations** — Record body composition data (weight, body fat %, muscle mass)
- **Attendance Tracking** — Daily log (present / late / absent) with streak counts

### ⚙️ For Admins
- **Full Admin Panel** — Separate auth guard (`/admin`), dedicated dashboard with platform-wide stats
- **Subscription Lifecycle** — Review, approve, or reject pending subscriptions; automated emails at each phase
- **Member Management** — Create, update, deactivate members; coach assignment with OTP verification
- **Plan & Pricing Control** — Create plans, set per-currency per-duration prices, toggle active/inactive
- **Coupon Management** — Create, enable/disable, and set max-uses on discount codes
- **Content Management** — Videos, before/after photos, testimonials — all with toggle active/inactive
- **Partner Logos** — Add, edit, reorder, and delete partner logos; show/hide the partners section
- **Maintenance Mode** — Take the site offline instantly with a branded 503 page, optional ETA countdown, and WhatsApp contact link; authenticated admins always bypass it
- **Settings Panel (10 tabs)** — Full control over every platform parameter without touching code

### 🌐 General
- **Bilingual (AR / EN)** — Full RTL Arabic and LTR English UI with a single locale toggle
- **Automated Emails** — Transactional emails at every key event (10 mailables)
- **Maintenance Mode** — Instant site-wide toggle; no-cache headers ensure browsers always see the current state
- **App Store Links** — Google Play and App Store download buttons in the hero and footer (configurable)
- **Fully Responsive** — Mobile, tablet, and desktop

---

## Admin Settings Panel

Accessible at `/admin/settings`, organized into **10 tabs**:

| Tab | What You Control |
|-----|-----------------|
| **الإعدادات العامة** | Site name, contact phone/email, WhatsApp number, location |
| **السوشيال ميديا** | Instagram, TikTok, YouTube, WhatsApp URLs; Google Play & App Store URLs |
| **الإحصائيات** | Hero success count, Why-Us card counts, testimonials stats, partners stats |
| **الفيديوهات** | Upload and toggle active/inactive coaching videos |
| **الشهادات** | Add, edit, reorder, toggle client testimonials |
| **قبل وبعد** | Upload before/after transformation photos |
| **الشريط المتحرك** | Arabic and English ticker text (newline-separated items) |
| **أقسام الصفحة** | Show/hide partners section; manage partner logos (add/edit/delete/reorder) |
| **وضع الصيانة** | Enable/disable maintenance mode, set custom message and ETA countdown |
| **نظام مكافأة العائلة** | Enable/disable referral reward, set discount mode (fixed/%), value, cap, eligible plan |

All settings are stored in the `settings` table and cached per-request. The cache flushes automatically after every save.

---

## Email System

| Trigger | Mailable | Recipient |
|---------|----------|-----------|
| Subscription submitted (guest) | `OrderReceivedMail` | Admin notification |
| Subscription submitted (logged in) | `OrderPendingReviewMail` | Client |
| Admin approves subscription | `OrderApprovedMail` | Client — "أكمل بياناتك" CTA for guests, dashboard link for registered |
| Admin rejects subscription | `OrderRejectedMail` | Client |
| Coach added via admin | `CoachOtpMail` | Coach — OTP to verify and activate account |
| Coach updates meeting link | `MeetingLinkMail` | Client — Google Meet link |
| Family invitation sent | `FamilyInvitationMail` | Invited friend — referral link |
| Subscription activated | `SubscriptionStartedMail` | Client |
| Subscription approaching expiry | `SubscriptionExpiryMail` | Client — renewal reminder |
| Renewal reminder | `SubscriptionReminderMail` | Client |

---

## Core Flows

### Subscription Lifecycle

```
  Client (logged in)              Guest (name + email only)
        │                                   │
        └──────────────┬────────────────────┘
                       ▼
              /purchase/{plan}
              Fill form → Submit
                       │
              status: pending_review
              Email → Admin notified
                       │
              ┌────────┴────────┐
         Approved            Rejected
              │                  │
              ▼                  ▼
       status: approved    status: rejected
       Email → Client      Email → Client
              │
   ┌──────────┴──────────────────────────────┐
   │  Guest? → auto-create account           │
   │  Email: "أكمل بياناتك" → set password  │
   │  → complete profile (phone, gender)     │
   └──────────┬──────────────────────────────┘
              │
              ▼
   Client books first session
   /schedule-meeting/{subscription}
              │
              ▼
   status: meeting_scheduled
   Coach receives booking → Confirm + add Meet link
   Email → Client with Google Meet URL
              │
              ▼
   status: active  ←── journey_started_at stamped
   Dashboard unlocked: progress logs, weight, evaluations
              │
              ▼
   Subscription expires → renewal emails sent
```

### Guest Checkout & Account Creation

```
  /purchase/{plan}
  Guest enters: name, email, phone, payment proof
        │
        ▼
  Subscription created (status: pending_review, guest_email set)
  Admin review queue updated
        │
  Admin approves
        │
        ├── Email with guest_email exists as User?
        │     YES → Link subscription to existing user
        │           Email: "أكمل بياناتك" → /auth/login
        │
        └── NO  → Create User (random password, email_verified)
                  Generate password-reset token
                  Email: "أكمل بياناتك" → /auth/reset-password/{token}
                  → User sets password → profile completion step
```

### Family Reward (Referral) Flow

```
  Client (active subscription)
        │
        ▼
  Dashboard → Enter friend's email → POST /family-invitations
        │
        ▼
  FamilyInvitation created (status: pending, expires in 7 days)
  FamilyInvitationMail sent to friend with referral link
        │
  Friend clicks link → /purchase/{eligible-plan}
  Referral discount auto-applied at checkout
        │
  Friend gets approved
        │
        ▼
  Invitation status: used
  Original client earns discount coupon
  Shown in dashboard "مكافأة العائلة" section
```

### Maintenance Mode

```
  Admin: Settings → وضع الصيانة → toggle ON
        │
        ▼
  Setting saved to DB → Setting::flushCache() called
        │
  Every non-admin web request → MaintenanceMode middleware
        │
        ├── Request is admin/* → bypass, serve normally
        ├── Admin guard authenticated → bypass
        │
        └── Visitor → 503 response + no-cache headers
              Branded page: custom message + ETA countdown + WhatsApp button
        │
  Admin toggles OFF → cache flushed → site live immediately
  (no-cache headers prevent browsers serving stale 200 responses)
```

### Multi-Currency Detection

```
  Visitor arrives
        │
        ├── session('currency') exists → use it
        │
        └── Fresh → DetectCurrency middleware → ip-api.com lookup
              SA → SAR  |  EG → EGP  |  TN → TND  |  Other → USD
              Saved in session
        │
  All prices → Plan::priceFor($currency, $months)
  Fallback chain: matching PlanPrice → SAR PlanPrice → plan->price
        │
  Symbol rendered via <x-web.currency-symbol :currency="$currency" />
  Manual switch: POST /currency/switch
```

---

## Tech Stack

### Backend
| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | ^8.2 | Core language |
| Laravel | 12.x | Framework |
| MySQL | 8.0+ | Database |

### Frontend
| Technology | Version | Purpose |
|------------|---------|---------|
| Tailwind CSS | 3.x | Utility-first styling |
| Alpine.js | CDN | UI reactivity |
| Vite | 7.x | Asset bundling |
| Swiper.js | 11.x | Sliders / carousels |
| GSAP | 3.12 | Scroll animations |
| Material Symbols | Google | Icon set |
| Cairo + Montserrat | Google Fonts | AR/EN typography |

### Middleware Stack (web group)
```
MaintenanceMode      ← 503 with admin bypass + no-cache headers on all responses
SetLocale            ← ar / en from session
DetectCurrency       ← SAR / EGP / TND / USD via IP lookup
```

---

## Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL >= 8.0

---

## Installation

### 1. Clone the repository
```bash
git clone https://github.com/Eng-AbdallahEmad/mindfitbro.git
cd mindfitbro
```

### 2. Install dependencies
```bash
composer install
npm install
```

### 3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure `.env`
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mindfitbro_db
DB_USERNAME=root
DB_PASSWORD=

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mail.spacemail.com
MAIL_PORT=465
MAIL_USERNAME=info@mindfitbro.com
MAIL_PASSWORD=your_password_here
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=info@mindfitbro.com
MAIL_FROM_NAME="MindFitBro"

# Session / Cache / Queue
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 5. Run migrations and seeders
```bash
php artisan migrate --seed
```

### 6. Build assets
```bash
npm run build
# or for development:
npm run dev
```

### 7. Start the server
```bash
php artisan serve
```
Open `http://localhost:8000`

---

## Database Schema

```
Core
────────────────────────────────────────────────────
users                    ← All users (client / coach roles; admin via guard)
user_profiles            ← Fitness profile (height, weight, DOB, gender)

Plans & Pricing
────────────────────────────────────────────────────
plans                    ← Subscription plans (name, icon, style_variant, sort_order)
features                 ← Plan features
feature_plan             ← Pivot: plan ↔ feature with sort_order
plan_prices              ← Per-currency per-duration prices
                           { plan_id, currency, duration_months, price }

Subscriptions & Cart
────────────────────────────────────────────────────
subscriptions            ← Full lifecycle: status, duration_months, currency,
                           guest fields, reviewed_by, journey_started_at
carts                    ← Shopping carts (currency-aware)
cart_items               ← Line items with price/duration snapshot
coupons                  ← Discount codes (value, max_uses, is_active)

Bookings & Coaching
────────────────────────────────────────────────────
meeting_bookings         ← Coach-client sessions (date, time, meet_link, status)
attendances              ← Daily attendance log (present / late / absent)
member_evaluations       ← Body composition records (weight, fat%, muscle%)
coach_ratings            ← End-of-journey client ratings

Programs & Progress
────────────────────────────────────────────────────
programs                 ← Training programs
program_days             ← Weekly schedule (workout / rest days)
user_workout_logs        ← Workout completion log
weight_logs              ← Weight history

Referrals
────────────────────────────────────────────────────
family_invitations       ← Referral invitations
                           { inviter_user_id, invitee_email, status, expires_at }

Content & Settings
────────────────────────────────────────────────────
settings                 ← Key-value platform settings grouped by tab
videos                   ← Coaching video links (sort_order, is_active)
testimonials             ← Client reviews (sort_order, is_active)
before_afters            ← Transformation photos (sort_order, is_active)
partners                 ← Partner logos (logo_path, sort_order, is_active)
```

---

## Project Structure

```
app/
├── Console/Commands/
│   └── ExpireFamilyInvitations.php       ← Scheduled command: expire old invites
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── AuthController.php        ← Register, login, password reset
│   │   │   └── AdminAuthController.php   ← Admin login (separate guard)
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── SubscriptionsController.php  ← Approve / reject lifecycle
│   │   │   ├── MembersController.php        ← Member CRUD + OTP flow
│   │   │   ├── CoachesController.php
│   │   │   ├── PlansController.php          ← Plans + features CRUD
│   │   │   ├── CouponsController.php
│   │   │   ├── SettingsController.php       ← 10-tab settings panel
│   │   │   ├── PartnersController.php       ← Partner logo CRUD
│   │   │   ├── VideosController.php
│   │   │   ├── TestimonialsController.php
│   │   │   ├── BeforeAftersController.php
│   │   │   └── FamilyInvitationsController.php
│   │   └── Web/
│   │       ├── HomeController.php
│   │       ├── PurchaseController.php        ← Purchase form + guest/auth submit
│   │       ├── GuestAccountController.php    ← Guest account completion flow
│   │       ├── ProfileController.php         ← Post-approval profile completion
│   │       ├── DashboardController.php       ← Client & coach dashboard
│   │       ├── BookingController.php         ← Session booking
│   │       ├── SubscriberController.php      ← Coach subscriber monitoring
│   │       ├── JourneyController.php         ← Journey view + PDF export
│   │       ├── FamilyInvitationController.php
│   │       └── CurrencyController.php        ← Manual currency switch
│   └── Middleware/
│       ├── MaintenanceMode.php               ← 503 + no-cache on all web responses
│       ├── SetLocale.php
│       └── DetectCurrency.php
├── Mail/                                     ← 10 mailable classes
├── Models/                                   ← 20+ Eloquent models
└── Services/Web/
    ├── HomeService.php
    ├── DashboardService.php
    ├── CoachDashboardService.php
    └── CurrencyService.php                   ← Currency detection, formatting, meta

resources/
├── views/
│   ├── layouts/web/app.blade.php            ← Main layout
│   ├── components/web/                      ← footer, navbar, currency-symbol, ...
│   ├── app/web/                             ← All public pages
│   ├── app/admin/                           ← Admin panel pages
│   ├── mail/                                ← Email templates
│   └── maintenance.blade.php                ← Standalone 503 page
├── lang/
│   ├── ar/messages.php
│   └── en/messages.php
```

---

## Subscription Plans

| Plan | Arabic Name | Base Price (SAR / 3 months) | Style |
|------|-------------|----------------------------|-------|
| 🥉 Starter | الأساسي | 299 SAR | Default |
| 🥇 Pro | النخبة | 599 SAR | Popular (highlighted) |
| 💎 Elite | إيليت | 999 SAR | Premium |

Prices per currency and duration are stored in `plan_prices` and resolved via `$plan->priceFor($currency, $months)`.

### Discount Coupons
```
MFB10 · MINDFITBRO · WELCOME · EID2025
```

---

## Key Routes

### Public
| Path | Method | Description |
|------|--------|-------------|
| `/` | GET | Homepage |
| `/purchase/{plan}` | GET/POST | Purchase form & submit |
| `/purchase/check-email` | POST | Check if guest email already has an account |
| `/purchase/check-coupon` | POST | Validate coupon code |
| `/purchase/success/{id}` | GET | Purchase confirmation |
| `/complete-account/{token}` | GET/POST | Guest account completion |
| `/complete-profile` | GET/POST | Profile completion after approval |
| `/calorie-calculator` | GET | Calorie calculator |
| `/locale/{lang}` | GET | Switch language (ar / en) |
| `/currency/switch` | POST | Switch display currency |

### Client (authenticated)
| Path | Method | Description |
|------|--------|-------------|
| `/dashboard` | GET | Client dashboard |
| `/dashboard/start-journey` | POST | Activate journey after first booking |
| `/schedule-meeting/{subscription}` | GET | Session booking page |
| `/booking/store` | POST | Submit booking |
| `/journey/{subscription}` | GET | Journey summary |
| `/journey/{subscription}/pdf` | GET | Download journey PDF |
| `/journey/{subscription}/rate` | POST | Rate coach |
| `/family-invitations` | POST | Send referral invitation |

### Coach (authenticated)
| Path | Method | Description |
|------|--------|-------------|
| `/coach/bookings` | GET | All booking requests |
| `/coach/bookings/{id}/confirm` | PATCH | Confirm + add Meet link |
| `/coach/bookings/{id}/reject` | PATCH | Reject booking |
| `/coach/subscribers` | GET | Subscriber list |
| `/coach/subscribers/{userId}` | GET | Subscriber detail |
| `/coach/subscribers/attendance` | POST | Log attendance |
| `/coach/subscribers/evaluation` | POST | Record evaluation |

### Admin (`/admin`)
| Path | Description |
|------|-------------|
| `/admin/dashboard` | Platform stats |
| `/admin/subscriptions` | Review queue |
| `/admin/subscriptions/{id}/approve` | Approve + send approval email |
| `/admin/subscriptions/{id}/reject` | Reject + send rejection email |
| `/admin/members` | Member management |
| `/admin/coaches` | Coach management |
| `/admin/plans` | Plan & feature management |
| `/admin/coupons` | Coupon management |
| `/admin/settings` | 10-tab settings panel |
| `/admin/partners` | Partner logo CRUD |
| `/admin/family-invitations` | Referral invitation list |

---

## Roadmap

- [x] Full admin control panel
- [x] Multi-currency pricing (SAR / EGP / TND / USD)
- [x] Maintenance mode with instant toggle
- [x] Family referral reward system
- [x] Journey PDF export
- [x] Automated email system (10 mailables)
- [x] App Store download buttons (hero + footer)
- [ ] Online payment gateway (Stripe / Moyasar)
- [ ] Advanced reports & analytics dashboard
- [ ] Mobile app

---

## Developer

**Abdallah Emad**
- GitHub: [@Eng-AbdallahEmad](https://github.com/Eng-AbdallahEmad)

---

<p align="center">
  Built with ❤️ to help people become the best version of themselves
</p>
