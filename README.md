# CareerCraft - Job Portal Web Application

## 📌 Overview

This is a Job Portal Web Application developed using PHP, HTML, CSS, and MySQL. The system allows users (job seekers) to create profiles, search for jobs, and apply, while employers can post jobs and manage applications. An admin panel is also included for managing users and job listings.

---

## 🚀 Features

### 👤 User (Job Seeker)

* User registration & login
* Profile creation and update
* Upload resume
* Browse/search jobs
* Apply for jobs
* Track application status

### 🏢 Company

* Employer registration & login
* Post new job listings
* Manage posted jobs
* View applicants

### 🛠️ Admin Panel

* Manage users and employers
* Approve/reject job posts
* Monitor platform activity

---

## 🧑‍💻 Technologies Used

* **Frontend:** HTML, CSS, JavaScript
* **Backend:** PHP
* **Database:** MySQL
* **Server:** Apache (XAMPP/WAMP)


---

## ⚙️ Installation Guide

1. Clone the repository:

```
git clone https://github.com/mrugansikathiriya/job-portal.git
```

2. Move the project folder to:

```
htdocs (for XAMPP)
```

3. Create a database in phpMyAdmin:

```
job_portal
```

4. Import the SQL file provided in the project.

5. Update database configuration in `config.php`:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "job_portal";
```

6. Start Apache & MySQL from XAMPP.

7. Open browser:

```
http://localhost/job-portal
```

---

## 🔐 Authentication

*  Secure login system with sessions
* "Remember Me" functionality using cookies
*  Basic form validation and authentication checks
---

## 📸 Some Screenshots
<img width="400" height="200" alt="image" src="https://github.com/user-attachments/assets/46664dc8-c24c-440c-b6a8-67ff43204e40" /><br><br>
<img width="400" height="200" alt="image" src="https://github.com/user-attachments/assets/b015f364-23ba-4a98-88bf-10b1864bb377" /><br><br>
<img width="400" height="200" alt="image" src="https://github.com/user-attachments/assets/11730bc7-5d58-4074-8ef7-1ded76e3ac61" /><br><br>
<img width="400" height="200" alt="image" src="https://github.com/user-attachments/assets/24889895-8556-4129-a2ee-d77284ff3cf4" /><br><br>

---

## 📈 Future Enhancements

* AI-based job recommendations
* Resume parser
* Chat system between employer and candidates

---


## 🙌 Acknowledgement

This project was developed as part of an academic internship/project.
We sincerely thank our mentors and organization for their guidance and support.
