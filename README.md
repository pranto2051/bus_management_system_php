# 🚌 IUBAT Bus Monitoring System

A comprehensive web-based bus tracking and management system designed for campus transportation. This system allows students to track bus routes, schedules, and live bus locations while providing administrators with tools to manage routes, buses, drivers, and schedules.

![Project Status](https://img.shields.io/badge/status-active-success)
![PHP Version](https://img.shields.io/badge/PHP-8.2+-blue)
![Database](https://img.shields.io/badge/Database-MySQL-orange)

---

## 📋 Table of Contents

- [Features](#-features)
- [Theme Color System](#-theme-color-system)
- [Login Details](#-login-details)
- [Screenshots](#-screenshots)
- [Database Schema](#-database-schema)
- [Installation](#-installation)
- [Technologies Used](#-technologies-used)
- [License](#-license)

---

## ✨ Features

### 👥 User Features

- **🔐 User Authentication**
  - Secure registration and login system
  - Password hashing using PHP `password_hash()`
  - Session management for user state

- **🗺️ Route Management**
  - Browse all available bus routes
  - View detailed route information including:
    - Complete stop list in order
    - Schedule times (departure/arrival)
    - Driver information
    - Bus assignments and capacity
  - Search routes by name or stop name
  - Add/remove routes from favorites

- **📊 Dashboard**
  - Personalized user dashboard
  - Live bus status (schedule-based)
  - Quick access to favorite routes
  - Route search by origin/destination
  - Notifications for route updates

- **📍 Map Integration**
  - Interactive map view (Leaflet + OpenStreetMap)
  - Visual representation of routes and stops
  - Real-time bus location tracking (when GPS data available)

- **👤 User Profile**
  - View and manage profile information
  - Update personal details

### 🔧 Admin Features

- **🚌 Bus Management**
  - Add, edit, and delete buses
  - Assign buses to routes and schedules
  - Set bus capacity

- **👨‍✈️ Driver Management**
  - Manage driver accounts
  - Assign drivers to schedules
  - Driver contact information

- **🛣️ Route Management**
  - Create and edit routes
  - Define route stops and their order
  - Manage route-stop relationships

- **⏰ Schedule Management**
  - Create bus schedules
  - Set departure and arrival times
  - Link schedules to routes and drivers

- **🚏 Stop Management**
  - Add, edit, and delete bus stops
  - Manage stop locations

- **📍 Location Tracking**
  - View bus location history
  - Monitor real-time bus positions (when GPS enabled)

- **👥 User Management**
  - View registered users
  - Monitor user activity

---

## 🎨 Theme Color System

The site now uses a shared CSS variable palette so the entire UI can be restyled from one place.

| Variable | Color Code | Use |
| --- | --- | --- |
| `--first-color` | `#93e4c1` | Light accent / soft surface tint |
| `--second-color` | `#3baea0` | Primary accent |
| `--third-color` | `#118a7e` | Primary dark / buttons / highlights |
| `--fourth-color` | `#1f6f78` | Deep accent / gradients |
| `--bg` | `#f2fbf7` | Main page background |
| `--bg-soft` | `#e8f6f1` | Soft background variant |
| `--surface` | `#ffffff` | Card and panel surface |
| `--surface-soft` | `#f7fcfa` | Soft surface background |
| `--surface-strong` | `#ddf4ec` | Strong accent surface |
| `--text` | `#17343c` | Main text color |
| `--text-strong` | `#0f2329` | Strong heading text |
| `--text-muted` | `#58747b` | Secondary text |
| `--border` | `rgba(31, 111, 120, 0.18)` | Borders and dividers |
| `--shadow` | `rgba(31, 111, 120, 0.14)` | Card and panel shadow |
| `--primary` | `var(--second-color)` | Primary alias |
| `--primary-dark` | `var(--third-color)` | Dark primary alias |
| `--accent` | `var(--fourth-color)` | Accent alias |
| `--accent-soft` | `var(--first-color)` | Soft accent alias |

### Utility Classes

- `.first-color` → `#93e4c1`
- `.second-color` → `#3baea0`
- `.third-color` → `#118a7e`
- `.fourth-color` → `#1f6f78`

---

## 🔐 Login Details

### Admin Login

- Username: `admin`
- Password: use the seeded admin password from `local_bus_monitoring.sql`
- Username: santo
- Password: santo

### User Login

- Username: `pranto`
- Password: use the seeded user password from `local_bus_monitoring.sql`

### Driver Login

- Username: the seeded `driver_name` value, for example `Rahim Uddin`
- Password: use the seeded driver password from `local_bus_monitoring.sql`

- Username: rohan
- Password: rohan
- 'rohan', '01700000000',

---

## 📸 Screenshots

### Landing Page

![Landing Page](assets/img/Screenshot%202025-12-17%20233059.png)

*Clean, modern landing page with a teal and green theme featuring hero section, call-to-action buttons, and feature highlights.*

### User Dashboard

![User Dashboard](assets/img/Screenshot%202025-12-17%20233138.png)

*Personalized user dashboard showing live bus info, favorite routes, notifications, and route search functionality.*

### Live Bus Map

![Live Bus Map](assets/img/Screenshot%202025-12-17%20233210.png)

*Interactive map view displaying bus locations with Leaflet.js integration. Shows last known positions of buses on the map.*

### Admin Dashboard

![Admin Dashboard](assets/img/Screenshot%202025-12-17%20233234.png)

*Admin panel dashboard with overview statistics, quick actions, recent activity, and live bus status monitoring.*

### Admin - Buses Management

![Admin Buses Management](assets/img/Screenshot%202025-12-17%20233252.png)

*Admin interface for managing buses, including add, edit, and delete functionality with complete bus details table.*

### ER Diagram

![Database ER Diagram](assets/img/WhatsApp%20Image%202025-12-17%20at%203.29.44%20PM.jpeg)

*Entity-Relationship diagram showing the complete database schema with all tables, relationships, and foreign keys.*

---

## 🗄️ Database Schema

The system uses a MySQL database with the following main entities:

### Core Tables

- **`user`** - Student/passenger accounts
- **`admin`** - Administrator accounts
- **`driver`** - Bus driver information
- **`route`** - Bus routes
- **`stop`** - Bus stop locations
- **`route_stops`** - Junction table linking routes to stops with order
- **`schedule`** - Bus schedules with departure/arrival times
- **`bus`** - Bus fleet information
- **`bus_location`** - GPS location tracking data
- **`user_favourite_route`** - User's favorite routes

### Key Relationships

- One route has many stops (via `route_stops`)
- One route has many schedules
- One schedule belongs to one route, one driver, and one admin
- One bus follows one route and one schedule
- One bus has many location records
- Many users can favorite many routes (many-to-many)

See the ER diagram above for complete relationship details.

---

## 🚀 Installation

### Prerequisites

- **XAMPP** (or any PHP/MySQL environment)
  - PHP 8.2 or higher
  - MySQL/MariaDB 10.4+
  - Apache web server

### Step-by-Step Setup

1. **Clone or Download the Project**
   ```bash
   git clone <repository-url>
   cd bus
   ```
   Or extract the project to your XAMPP `htdocs` folder:
   ```
   C:\xampp\htdocs\bus
   ```

2. **Database Setup**
   - Open phpMyAdmin (`http://localhost/phpmyadmin`)
   - Create a new database named `local_bus_monitoring`
   - Import the schema file:
     - Go to "Import" tab
     - Select `local_bus_monitoring.sql`
     - Click "Go"
   - (Optional) Import sample data:
     - Import `seed_dummy_data.sql` for dummy data

3. **Database Configuration**
   - Edit `includes/db.php` if your database credentials differ:
   ```php
   $host = "localhost";
   $user = "root";
   $password = "";  // Your MySQL password
   $dbname = "local_bus_monitoring";
   ```

4. **Start XAMPP**
   - Start Apache and MySQL from XAMPP Control Panel

5. **Access the Application**
   - Open browser: `http://localhost/bus/`
   - Use the login details listed in the [Login Details](#-login-details) section above.

---

## 🛠️ Technologies Used

### Backend
- **PHP 8.2+** - Server-side scripting
- **MySQL/MariaDB** - Relational database
- **MySQLi** - Database connectivity

### Frontend
- **HTML5** - Markup
- **CSS3** - Styling
- **JavaScript** - Client-side interactivity
- **Leaflet.js** - Map library
- **OpenStreetMap** - Map tiles
- **Font Awesome** - Icons

### Tools & Libraries
- **XAMPP** - Development environment
- **phpMyAdmin** - Database management

---

## 🎯 Key Features Explained

### Route Details Page
- Access via: `/pages/routes.php?route_id=X`
- Shows complete route information:
  - Numbered stop list
  - All schedules with times
  - Driver details
  - Bus information
  - Favorite toggle

### Live Bus Status
- Currently schedule-based simulation
- Shows status like "On route", "Starts in X min", "Arrived X min ago"
- Can be upgraded to real GPS tracking

### Map Integration
- Uses Leaflet.js for interactive maps
- OpenStreetMap tiles (free, no API key needed)
- Shows routes and stops
- Ready for GPS bus location markers

---

## 📝 Future Enhancements

- [ ] Real-time GPS tracking integration
- [ ] Mobile app (React Native / Flutter)
- [ ] Push notifications for bus arrivals
- [ ] Booking system for seats
- [ ] Route optimization algorithms
- [ ] Analytics dashboard
- [ ] Multi-language support
- [ ] Dark mode theme

---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

**Made with ❤️ for better campus transportation**








User Login

- Username: `pranto`
- Password: use the seeded user password from `local_bus_monitoring.sql`

user mail: sanzit@gmail.com
password: sanzit12345

Give Me SQL code# bus_management_system_php
