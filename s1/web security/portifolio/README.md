# tob.zico // Neo-Brutalist Binary Portfolio & Admin Console

A production-grade, highly responsive developer portfolio and content management system built with **Core PHP 8+**, **MySQL**, **vanilla JavaScript**, and a **Neo-Brutalist 0s & 1s design language**.

---

## 🎨 Design System & Color Palette

Designed according to brand identity specifications:

| Color Token | Hex Code | Purpose |
| :--- | :--- | :--- |
| **COAL** | `#0F0F10` | Dark root canvas background |
| **GRAPHITE** | `#1E1E22` | Card surfaces, modals & admin sidebar |
| **SAND** | `#E7D6C1` | Primary text, high-contrast borders & primary CTA |
| **VIOLET** | `#6E5A73` | Card shadows & secondary accents |
| **TERRACOTTA**| `#E07A4F` | High-visibility action buttons & highlights |
| **STONE** | `#A89C8F` | Metadata tags, technical borders & muted accents |

- **Shape Language**: Right angles, raw grid lines, cut corners (`clip-path`), system dot matrix backgrounds, pixel art avatar board, and binary text decoders.
- **Scroll & Visual Animations**: Interactive 60fps canvas binary stream (0s and 1s) that disperses on cursor movement, smooth viewport reveal triggers, and binary text scramble on scroll.

---

## 🚀 Quick Start & Database Setup

### 1. Database Initialization
Ensure your MySQL service is running (e.g. MySQL 8.0, XAMPP, Laragon, or Docker).

Option A: **1-Click Web Migration**:
Navigate in your browser to:
```
http://localhost/database/setup.php
```
Click **"START MIGRATION"** to automatically create the `portfolio_db` database, establish all normalized relational tables, foreign key constraints, and seed all initial projects, skills, articles, talks, and achievements.

Option B: **CLI Migration**:
```bash
php database/setup.php
```

Option C: **Import via MySQL CLI or Workbench**:
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p portfolio_db < database/seed.sql
```

### 2. Run Local Development Server
From the project root:
```bash
php -S localhost:8000
```
Then visit:
- **Public Portfolio**: `http://localhost:8000/`
- **Admin Gateway**: `http://localhost:8000/admin/login.php`

---

## 🛡️ Admin Credentials

| Parameter | Default Credential |
| :--- | :--- |
| **Username** | `admin` |
| **Email** | `admin@portfolio.local` |
| **Password** | `Admin@12345` |
| **Hashing Engine** | `PASSWORD_BCRYPT` via `password_hash()` |

---

## 🏗️ Architecture & Technical Features

### 1. Security & Protection
- **PDO Prepared Statements**: 100% of queries use prepared statements with parameterized inputs. SQL injection is mitigated at the root.
- **CSRF Defense**: Cryptographic token generation (`bin2hex(random_bytes(32))`) validated with `hash_equals()` on all state-altering forms.
- **Session Hardening**: `session_regenerate_id(true)` executed upon login to stop session fixation; `httponly`, `samesite=Strict` cookie policies.
- **XSS Prevention**: Strict HTML escaping with `htmlspecialchars(..., ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`.
- **File Upload Safeguards**: MIME validation via PHP `finfo`, extension whitelisting, randomized hex filenames, and `.htaccess` script execution prevention in `/uploads/`.
- **Directory Protection**: Root `.htaccess` blocks direct access to `.env`, `database/`, and `config/`.

### 2. Complete Admin CRUD
- **Profile Management**: Update identity credentials, biography, phone, email, social links, resume URL, and avatar image.
- **Projects Management**: Add, edit, purge, upload covers, tag technologies, edit bullet lists, set featured status, change display order, and toggle draft/published.
- **Skills Matrix**: Categorize technologies across Languages, Frameworks, and Domains with numerical proficiency ratings.
- **Achievements & Honors**: Manage awards, levels (State/National), years, citations, and events.
- **Talks & Sessions**: Manage keynote records, session dates, descriptions, and video recording links.
- **Articles & Papers**: Full editorial system with URL slug generation, HTML content, summaries, cover images, and topic tags.
- **Creative Works (Lines & Layers)**: Interactive multi-page book viewer ("Y SHE Y") and visual artwork gallery.
- **Contact Inquiries**: Real-time terminal message queue with status toggles (read/unread), full payload viewer, and purge options.
- **Site Settings**: Live updates to site titles, telemetry indicators, status flags, and footer colophon.

### 3. Verification Suite
Run the assert-based test check with zero external dependencies:
```bash
php test_check.php
```
Verifies XSS escaping, CSRF tokens, slug creation, BCRYPT hash validation, SQL schema artifacts, and upload constraints.
