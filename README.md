# AGEN — Academic Grade Management System

**Web-Based Academic Management and Student Performance Tracking Platform**

## Overview

AGEN (Academic Grade Management System) is an academic web application developed to simplify and centralize educational administration.

The platform provides an integrated environment for managing student records, academic grades, class schedules, and student performance reports.

It also includes dashboards that help users visualize academic information, monitor student results, and manage educational activities.

This project demonstrates practical experience in full-stack web development, relational database management, authentication, data visualization, and automated document generation.

> **Disclaimer:** AGEN is an academic prototype developed for educational purposes. It is not intended for production deployment without additional security testing and configuration.

## Project Demonstration

### Video Demo

A video demonstration of **AGEN — Academic Grade Management System** is available to showcase the application's interface and academic management workflows.



https://github.com/user-attachments/assets/da040486-803a-4f37-8044-ac02aa80b16d



The demonstration highlights the main functionalities of the platform, including:

- User authentication and navigation.
- Student management and academic records.
- Grade entry and consultation.
- Academic performance monitoring.
- Interactive dashboards and statistics.
- Student report card generation.
- Class schedule management.

## Key Features

### 1. Student Management

- Add and manage student records.
- Access detailed student information.
- Organize students by academic program and level.
- Centralize academic information through dedicated management interfaces.

### 2. Grade Management

- Enter and consult student grades.
- Organize grades by academic module.
- Calculate academic averages.
- Monitor student performance.
- Identify and display top academic results.

### 3. Academic Dashboards

- Visualize academic statistics.
- Display student performance indicators.
- Monitor results by class.
- Access administrative dashboards.
- Present academic data using Chart.js.

### 4. PDF Report Card Generation

- Generate individual student report cards.
- Consult academic results in PDF format.
- Generate class-level report cards.
- Automate document creation using the FPDF library.

### 5. Schedule Management

- Access class schedules.
- Manage timetable information.
- Save schedule-related data through dedicated application components.

### 6. Authentication and Session Management

- User login and logout.
- PHP session-based authentication.
- Dedicated user interfaces for academic administration.

## Technology Stack

| Category | Technologies |
|---|---|
| Backend | PHP |
| Database | MySQL / MariaDB |
| Frontend | HTML5, CSS3, JavaScript |
| UI Framework | Bootstrap |
| Data Visualization | Chart.js |
| PDF Generation | FPDF |
| Local Development | XAMPP / WampServer |
| Database Administration | phpMyAdmin |

## Project Architecture

```text
                  USER
                    |
                    v
             Web Interface
        HTML / CSS / JavaScript
                    |
                    v
             PHP Application
                    |
          +---------+---------+
          |                   |
          v                   v
    Authentication       Academic Modules
          |                   |
          |         +---------+---------+
          |         |         |         |
          |         v         v         v
          |      Students   Grades   Schedules
          |         |         |         |
          +---------+---------+---------+
                    |
                    v
             MySQL Database
                    |
          +---------+---------+
          |                   |
          v                   v
    Academic Dashboard    PDF Reports
          |                   |
          v                   v
       Chart.js              FPDF
```

## Project Structure

```text
academic-grade-management-system/
│
├── README.md
├── .gitignore
├── config.example.php
├── login.sql
│
├── login.php
├── logout.php
├── verify.php
├── home.php
│
├── dashboard.php
├── dashboard_secretaire.php
│
├── ajouter_etudiant.php
├── gestion_etudiants.php
├── details_etudiant.php
│
├── gestion_notes.php
├── grades.php
│
├── bulletin_etudiant.php
├── bulletins.php
├── selection_bulletins.php
├── generate_class_bulletins.php
│
├── schedule.php
├── save_schedule.php
├── emploi.html
│
├── vacation.php
├── test.php
│
└── fpdf186/
```

## Installation and Setup

### Prerequisites

- PHP 8 or a compatible version.
- MySQL or MariaDB.
- Apache Web Server.
- XAMPP, WampServer, or an equivalent PHP development environment.
- Git.

### Step 1 — Clone the Repository

```bash
git clone https://github.com/YOUR_USERNAME/academic-grade-management-system.git
```

Replace `YOUR_USERNAME` with your GitHub username.

### Step 2 — Configure the Local Web Server

Copy the project folder into the local web server directory.

For XAMPP on Windows:

```text
C:\xampp\htdocs\academic-grade-management-system
```

Start Apache and MySQL from the XAMPP Control Panel.

### Step 3 — Configure the Database

1. Open phpMyAdmin at `http://localhost/phpmyadmin`.
2. Create a local database named `login`.
3. Import the provided `login.sql` file.
4. Verify that the database tables have been created.

**Note:** The provided SQL file contains an `enseignants` table and sample records. Additional tables required by other modules may need to be configured separately.

### Step 4 — Configure Database Connection

Copy:

```text
config.example.php
```

to:

```text
config.php
```

Update the database connection settings according to your local environment.

Keep database credentials private and never commit real passwords to GitHub.

### Step 5 — Run the Application

Open your browser and navigate to:

```text
http://localhost/academic-grade-management-system/
```

You can also open the login page directly:

```text
http://localhost/academic-grade-management-system/login.php
```

Access the available academic management modules through the application interface.

## Main Application Modules

| Module | Description |
|---|---|
| Authentication | Login, logout, and user session management |
| Student Management | Student registration and academic records |
| Grade Management | Grade entry, consultation, and average calculations |
| Dashboard | Academic statistics and performance indicators |
| Report Cards | Individual and class-level PDF generation |
| Schedule Management | Academic timetable management |

## Security Considerations

As an academic web application, AGEN requires additional security measures before production use.

Recommended improvements include:

- Secure password hashing using PHP's `password_hash()` and `password_verify()`.
- Prepared SQL statements to prevent SQL injection.
- Role-based access control (RBAC).
- CSRF protection for sensitive operations.
- Input validation and output escaping.
- Secure PHP session configuration.
- Protection of database credentials.
- Authentication and authorization testing.
- Removal of test accounts and sensitive academic data.

## Current Limitations

- The supplied SQL script does not include every table potentially required by the application.
- Local database configuration is necessary.
- The application has not been validated for production deployment.
- Security testing and additional hardening are required.
- Some modules depend on the availability of supporting database tables and records.

## Future Improvements

- Implement complete role-based access control.
- Improve database schema documentation.
- Add automated unit and integration tests.
- Strengthen authentication and session security.
- Implement additional academic analytics.
- Add automated backup and recovery mechanisms.
- Introduce CI/CD pipelines.
- Improve deployment automation.
- Perform comprehensive web application security testing.

## Skills Demonstrated

- Full-stack web application development.
- PHP backend programming.
- MySQL relational database integration.
- CRUD operations.
- Authentication and session management.
- Academic data visualization.
- Automated PDF report generation.
- Web application architecture.
- Security awareness in web application development.

## Author

**Asma Bensassi Nour**

Cyberdefense Engineering Student — ENSA Marrakech

Academic project focused on web application development, database management, and academic information systems.

---

**AGEN — Simplifying Academic Management Through Digital Solutions.**
