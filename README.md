<div align="center">
  <h1>QayemTech</h1>
  <p><strong>The Future of AI-Driven Performance Management</strong></p>
</div>

<p align="center">
  <strong>A Next-Generation HR & Performance Management SaaS Platform</strong><br>
  Built with Laravel, powered by Google Gemini AI, and meticulously designed with a premium Glassmorphism UI.
</p>

<p align="center">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white">
  <img alt="MySQL" src="https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white">
  <img alt="Bootstrap" src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white">
  <img alt="Stripe" src="https://img.shields.io/badge/Stripe-626CD9?style=for-the-badge&logo=Stripe&logoColor=white">
</p>

<hr>

## 🚀 Overview

**QayemTech** is an innovative SaaS platform designed to transform how modern companies manage their human resources and employee performance. It moves beyond traditional, rigid HR systems by integrating real-time AI assistance, dynamic visual analytics, and a visually stunning "Dark Glassmorphism" interface.

Whether you're a General Manager, HR Specialist, or Department Manager, QayemTech provides a unified, intelligent, and seamless experience tailored to your role.

## ✨ Key Features

- 🤖 **AI HR Assistant (Gemini 2.0 Flash):** A built-in, context-aware AI assistant that can answer questions, analyze data, and provide real-time HR insights.
- 📊 **Visual Analytics:** Interactive, real-time performance trends and statistics powered by Chart.js.
- 🏢 **Hierarchical Management:** Efficiently manage Companies, Departments, Managers, and Employees with specialized roles and permissions.
- 🎨 **Premium UI/UX:** A high-end "Dark Glassmorphism" design featuring smooth scroll-reveals, responsive layouts, and interactive micro-animations.
- 💳 **Subscription Management:** Seamless Stripe integration for handling dynamic SaaS plans (Basic, Pro, Enterprise, etc.).
- 🌍 **Fully Localized (RTL & LTR):** Full support for both Arabic and English markets with on-the-fly language switching.

## 🛠 Tech Stack

- **Backend:** [Laravel](https://laravel.com/) (PHP)
- **Frontend & Admin Panel:** Blade Templates, [Filament PHP](https://filamentphp.com/), Bootstrap 5, Custom CSS Animations
- **Database:** MySQL
- **AI Integration:** Google Gemini API (gemini-2.0-flash)
- **Payments:** Stripe API
- **Charts:** Chart.js

## 📦 Requirements

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js & NPM
- Stripe Account (for subscriptions)
- Google Gemini API Key (for AI features)

## ⚙️ Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/MoWalid-7/qayem_tech_repo.git
   cd qayem_tech_repo
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install NPM dependencies:**
   ```bash
   npm install
   npm run build
   ```

4. **Environment Setup:**
   Copy the `.env.example` file to `.env` and configure your database, Stripe, and Gemini credentials:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Make sure to set your `GEMINI_API_KEY`, `STRIPE_KEY`, and `STRIPE_SECRET`.*

5. **Run Migrations & Seeders:**
   ```bash
   php artisan migrate --seed
   ```

6. **Serve the Application:**
   ```bash
   php artisan serve
   ```
   Visit `http://localhost:8000` in your browser.

## 👥 User Roles

- **General Manager (GM):** Full access to the company dashboard, billing, and HR team management.
- **Department Manager:** Manages specific departments and evaluates employees within those departments.
- **HR Specialist:** Focuses on employee onboarding, performance reviews, and general HR tasks.

## 🔒 Security

QayemTech implements robust security measures:
- CSRF protection on all forms.
- Role-based Access Control (RBAC).
- Secure payment processing via Stripe.
- Encrypted password storage.

## 📄 License

This project is proprietary software. All rights reserved by **QayemTech**.
