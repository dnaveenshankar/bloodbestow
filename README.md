# Blood Bestow - Blood Donation and Request Management System

## Overview:
Blood Bestow is a web-based blood donation and request management system. It allows patients to request blood, and donors to register and donate blood. Patients can also view the status of their requests and view donor details once their requests are approved.

## Requirements:
1. **XAMPP (Local Server)**
   - Download and install XAMPP from https://www.apachefriends.org/index.html.
   - Start the Apache and MySQL modules in XAMPP.

2. **Browser**
   - Use any modern browser (e.g., Google Chrome, Mozilla Firefox, Microsoft Edge) to access the system.

## Setup Instructions:
1. **Clone or Download the Project:**
   - Download or clone the project files and place them in the `htdocs` directory of your XAMPP installation (usually `C:\xampp\htdocs` on Windows).

2. **Database Setup:**
   - Open `phpMyAdmin` by navigating to `http://localhost/phpmyadmin` in your browser.
   - Create a new database called `bloodbestow`.
   - Import the database file `bloodbestow.sql` (if provided) to set up the necessary tables.
   - Alternatively, create the required tables manually as per the project's database schema.

3. **Database Configuration:**
   - Open the `db_connection.php` file and update the database connection details as needed:
     ```
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "bloodbestow";
     ```
   - Ensure that `root` is the default user for local databases and the password field is empty.

4. **Starting the Project:**
   - In your browser, navigate to `http://localhost/bloodbestow` to access the project homepage.

5. **Using the Application:**
   - **Patient Login:** Use `login-patient.php` to log in as a patient and view your blood requests.
   - **Donor Login:** Use `donor-login.php` to log in as a donor and manage your donation details.

## Features:
- **Patient:**
  - Submit blood requests.
  - View request status and donor information (when approved).
  
- **Donor:**
  - Register as a donor.
  - View blood requests and accept or reject them.

- **Admin:** (optional)
  - Manage requests and donors through an administrative dashboard.

## Troubleshooting:
- If you encounter any issues accessing the project, ensure that XAMPP's Apache and MySQL services are running.
- Check the `db_connection.php` file to ensure that the database connection details are correct.

## Contact:
For further assistance, please contact me at dnaveenshankar2003@gmail.com.
