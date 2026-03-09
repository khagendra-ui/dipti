# CareOnCall - On-Demand Caretaker Booking System

## 📋 Project Overview

CareOnCall is a comprehensive web-based platform designed to connect reliable caretakers with families in need of care services. The system provides three distinct user roles with specific features and functionalities.

## ✨ Key Features

### Client Features
- User registration and secure login
- Browse verified caretakers with detailed profiles
- Advanced search functionality
- Easy booking system with date/time selection
- Booking history and management
- Rate and review caretakers
- Real-time booking status tracking

### Caretaker Features
- Professional profile creation
- Document verification system
- Set weekly availability schedule
- Receive and manage booking requests
- Accept/reject bookings with custom responses
- View upcoming appointments
- Track earnings and reviews

### Admin Features
- Secure admin dashboard
- Verify caretaker applications and documents
- User management (clients & caretakers)
- Booking monitoring and management
- System activity logging
- Statistical overview

## 🛠️ Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript (Vanilla JS)
- **Backend:** PHP 7.0+
- **Database:** MySQL
- **Server:** Apache (via XAMPP)
- **Development:** Visual Studio Code

## 📁 Project Structure

```
CareOnCall/
├── index.php                 # Home page
├── php/
│   ├── config.php           # Database configuration & utilities
│   └── logout.php           # Logout handler
├── pages/
│   ├── login.php            # User login
│   ├── register.php         # User registration
│   ├── client_dashboard.php # Client dashboard
│   ├── caretaker_dashboard.php # Caretaker dashboard
│   ├── admin_dashboard.php  # Admin dashboard
│   ├── browse_caretakers.php # Search & browse caretakers
│   ├── book_caretaker.php   # Create booking
│   ├── my_bookings.php      # View bookings
│   ├── profile.php          # User profile management
│   ├── availability.php     # Caretaker availability
│   ├── respond_booking.php  # Caretaker booking response
│   ├── verify_caretaker.php # Admin verification
│   ├── manage_users.php     # Admin user management
│   ├── manage_caretakers.php # Admin caretaker management
│   ├── manage_bookings.php  # Admin booking management
│   └── admin_logs.php       # Activity logging
├── css/
│   └── style.css            # Complete styling
├── js/
│   └── booking.js           # JavaScript interactivity
├── uploads/                 # User uploads (documents, pictures)
├── database/
│   └── schema.sql           # Database schema
└── README.md               # This file
```

## 🚀 Installation & Setup

### Prerequisites
- XAMPP (Apache + MySQL + PHP) installed
- Visual Studio Code
- PHP 7.0 or higher
- MySQL 5.7 or higher

### Step-by-Step Installation

#### 1. **Extract Project Files**
```bash
# Copy the CareOnCall folder to xampp/htdocs/
# C:\xampp\htdocs\CareOnCall\
```

#### 2. **Start XAMPP Services**
- Open XAMPP Control Panel
- Start Apache
- Start MySQL

#### 3. **Create Database**
- Open phpMyAdmin (http://localhost/phpmyadmin)
- Create a new database named `careoncall`
- Import the schema from `database/schema.sql`:
  - Right-click on the `careoncall` database
  - Select "Import"
  - Choose `database/schema.sql`
  - Click "Go"

#### 4. **Configure Database Connection**
- Open `php/config.php`
- Verify database credentials:
  ```php
  define('DB_HOST', 'localhost');
  define('DB_USER', 'root');
  define('DB_PASS', '');  // Usually empty for XAMPP
  define('DB_NAME', 'careoncall');
  ```

#### 5. **Create Uploads Directory**
- Ensure `uploads/` folder exists and has write permissions
- Chmod to 755 on Linux/Mac or allow write permissions on Windows

#### 6. **Access the Application**
- Open browser and navigate to: `http://localhost/CareOnCall/`

## 👥 Default Admin Account

```
Email: admin@careoncall.com
Password: admin@123
```

⚠️ **Important:** Change the default admin password immediately after login!

## 📊 Database Schema

### Main Tables
- **users** - All user accounts (clients, caretakers, admins)
- **caretaker_details** - Extended caretaker information and verification status
- **caretaker_availability** - Weekly availability schedule
- **bookings** - Booking records with status tracking
- **booking_requests** - Caretaker response workflow
- **reviews** - User ratings and feedback
- **admin_logs** - Activity audit trail

## 🔐 Security Features

- Password hashing with BCRYPT
- Session-based authentication
- SQL injection prevention (prepared statements)
- User role-based access control
- Admin activity logging
- Input validation and sanitization

## 📝 How to Use

### For Clients
1. Register as a Client
2. Browse available caretakers
3. Click "View Details" or "Book Now"
4. Select date, time, and location
5. Review estimated cost and confirm booking
6. Wait for caretaker confirmation
7. Provide feedback after service completion

### For Caretakers
1. Register as a Caretaker
2. Complete your profile with skills and experience
3. Verify your account (wait for admin approval)
4. Set your weekly availability
5. Receive booking requests from clients
6. Accept or reject requests
7. View upcoming bookings
8. Receive reviews from clients

### For Admins
1. Login with admin credentials
2. Dashboard shows system statistics
3. Review pending caretaker applications
4. Approve or reject caretakers
5. Manage users and bookings
6. Monitor system activity

## 🐛 Troubleshooting

### Database Connection Error
- Ensure MySQL is running
- Check credentials in `php/config.php`
- Verify database `careoncall` exists

### Can't Upload Files
- Check `uploads/` folder exists
- Verify file permissions (755 or writable)
- Check max upload size in `php.ini`

### Login Issues
- Clear browser cookies
- Check if user exists in database
- Verify password is correct
- Check user status is "active"

### Pages Not Loading
- Ensure all files are in correct folders
- Check file permissions
- Verify PHP is enabled
- Check browser console for JavaScript errors

## 📞 Contact & Support

For issues or questions, check the following:
- Verify XAMPP services are running
- Check file paths in configuration
- Ensure database is properly imported
- Review browser console for errors

## 📄 License

This project is created for educational purposes.

## 🎯 Future Enhancements

Potential features for future versions:
- Payment integration (Stripe, PayPal)
- Email notifications
- SMS notifications
- Real-time chat
- Video verification
- Background checks
- Insurance verification
- Mobile app
- Google Maps integration
- Advanced analytics dashboard

---

**Version:** 1.0.0  
**Last Updated:** March 2024

