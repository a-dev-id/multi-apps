# a-dev Multi Apps - Nandini Hospitality Management System

A comprehensive **Laravel 12 + Filament 4 admin panel** for automating guest communication in hospitality businesses. Built by **a-dev** for **Nandini Jungle by Hanging Gardens**, this system manages transactional emails, newsletters, and guest communications through a queue-based architecture.

## 🎯 System Overview

**a-dev Multi Apps** is a modular automation platform that handles three core communication workflows:

1. **Guest Letter System** - Automated booking confirmations, pre-arrival, and post-stay emails
2. **Newsletter Management** - Campaign creation, subscriber management, and email tracking
3. **Birthday Notifications** - Automated birthday emails to guests and subscribers

All communications are sent asynchronously via a queue system with full tracking and error handling.

## ✨ Key Features

### Guest Letter Automation
- Automatic confirmation letters when bookings are created
- Scheduled pre-arrival emails (3 days before check-in)
- Post-stay thank you emails with guest personalization
- Manual letter creation for direct guest communication
- Email status tracking (pending, sent, failed)
- Full guest data management

### Newsletter Management
- Campaign creation and scheduling
- Subscriber management with tagging
- Guest import from booking system
- Duplicate detection and merging
- Bounce list management
- Email open tracking and analytics
- CSV import/export functionality

### Birthday Notifications
- Automatic birthday email detection
- Scheduled birthday greetings
- Integration with subscriber database
- Annual send tracking

## 🏗️ Architecture

The application uses a **modular domain-driven design**:

```
app/Modules/
├── Newsletter/          # Newsletter campaigns & subscribers
├── GuestLetter/        # Booking communications
└── Birthday/           # Birthday automation
```

**Key Technologies:**
- Laravel 12 framework
- Filament 4 admin panel
- Database queue system
- Eloquent ORM with relationships
- Blade email templates
- Schedule-based automation

## 🚀 Getting Started

### Prerequisites
- PHP 8.2+
- MySQL/MariaDB
- Composer
- Node.js (for frontend assets)

### Installation

```bash
# Clone repository
git clone <repository-url>
cd nandini-apps

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate

# Build assets
npm run build
```

### Development

```bash
# Start development server with queue listener
composer run dev

# Or run separately:
php artisan serve
php artisan queue:listen
npm run dev
```

### Production Queue Processing

```bash
# Process queued jobs
php artisan queue:work --stop-when-empty --max-jobs=20 --max-time=50

# Or use scheduler
php artisan schedule:work
```

## 📋 Database Schema

### Core Tables
- `gl_guest_letter_sends` - Email tracking for guest letters
- `subscribers` - Newsletter subscriber management
- `guests` - Guest information
- `bookings` - Reservation records
- `birthday_sends` - Birthday email tracking
- `newsletters` - Campaign data

## 🎨 Admin Dashboard

Access the admin panel at `/admin`:
- Guest Letter management
- Newsletter campaigns
- Subscriber management
- Birthday tracking
- Email statistics and reports

### Main Features
- Drag-and-drop interface
- Real-time status updates
- Advanced filtering and search
- Bulk actions
- Email preview
- Import/export tools

## 📧 Email System

All emails are template-based and customizable:
- **Confirmation Letters** - On booking creation
- **Pre-Arrival Letters** - 3 days before check-in
- **Post-Stay Letters** - After checkout with guest personalization
- **Newsletters** - Scheduled campaigns
- **Birthday Greetings** - Annual birthday emails

Email templates support:
- Guest name personalization
- HTML and plain text
- Dynamic data from database
- Attachments

## 🔧 Configuration

Key environment variables:

```env
# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@nandini.com

# Queue Configuration
QUEUE_CONNECTION=database

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=nandini_apps
DB_USERNAME=root
DB_PASSWORD=
```

## 📚 Documentation

- **[Copilot Instructions](.github/copilot-instructions.md)** - Detailed architecture and development guide
- **[Database Schema](DATABASE_SCHEMA.md)** - Complete schema documentation
- **[Deployment Checklist](DEPLOYMENT_CHECKLIST.md)** - Pre-deployment verification steps

## 🛠️ Development

### Project Structure
```
app/
├── Modules/          # Domain modules (Guest Letter, Newsletter, Birthday)
├── Filament/         # Admin interface resources
├── Mail/             # Email classes
├── Models/           # Eloquent models
├── Jobs/             # Queue jobs
└── Providers/        # Service providers

routes/
├── web.php           # Web routes & cron endpoints
└── console.php       # Console commands

resources/views/
└── emails/           # Email templates
```

### Code Conventions
- Module-based architecture for separation of concerns
- Eloquent relationships for data integrity
- Queue jobs for asynchronous processing
- Filament schemas for admin forms/tables
- Blade templates for emails

## 🧪 Testing

```bash
# Run tests
composer run test

# Run specific test file
php artisan test tests/Feature/GuestLetterTest.php
```

## 📈 Recent Updates (January 15, 2026)

- ✨ Guest name personalization in post-stay emails
- ✨ Manual post-stay letter creation without bookings
- ✨ Guest import from booking system to subscribers
- 🎨 Optimized admin interface with dropdown menu for tools
- 🐛 Fixed duplicate detection and guest filtering
- 📊 Enhanced subscriber management interface

## 🔒 Security

- All emails processed through secure queue system
- Database-backed email job storage
- Error logging and notification
- Failed email retry mechanism
- Input validation and sanitization

## 📞 Support & Contact

**Developed by:** a-dev  
**For:** Nandini Jungle by Hanging Gardens

For issues or feature requests, please contact the development team.

## 📄 License

This application is proprietary software. All rights reserved.

---

**Last Updated:** January 15, 2026  
**Version:** 1.0.0  
**Status:** Production Ready
