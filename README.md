# 🏢 Employee Profile Management System

### KPU Kota Surabaya

<p align="center">
  <img src="https://via.placeholder.com/800x400.png?text=Dashboard+Preview" alt="Dashboard Preview"/>
</p>

---

## 📌 Overview

This project is a full-stack web-based Employee Profile Management System developed for KPU Kota Surabaya. It helps manage employee data efficiently and provides PDF-based reporting for easy documentation.

---

## ✨ Features

* 🔐 Secure authentication (bcrypt hashing)
* 👨‍💼 Employee data management (CRUD)
* 🧑‍💻 Admin dashboard
* 📄 Export employee data to PDF
* 🔔 Context-based notifications

---

## 🖼️ System Preview

### 🔐 Login Page

<p align="center">
  <img src="https://via.placeholder.com/600x300.png?text=Login+Page" alt="Login Page"/>
</p>

### 📊 Dashboard Admin

<p align="center">
  <img src="https://via.placeholder.com/600x300.png?text=Admin+Dashboard" alt="Dashboard"/>
</p>

### 👥 Employee Management

<p align="center">
  <img src="https://via.placeholder.com/600x300.png?text=Employee+Management" alt="Employee"/>
</p>

### 📄 PDF Export Feature

<p align="center">
  <img src="https://via.placeholder.com/600x300.png?text=PDF+Export" alt="PDF Export"/>
</p>

---

## 🧠 System Design

* ERD (Entity Relationship Diagram)
* ~20 relational tables (MySQL)
* Role-based system (Admin & Employee)

---

## 🛠️ Tech Stack

* Frontend: HTML, CSS, JavaScript
* Backend: PHP (Native)
* Database: MySQL
* Design: Figma

---

## ⚙️ Installation

```bash
git clone https://github.com/your-username/your-repo.git
```

1. Move project to `htdocs`
2. Import database (`.sql`)
3. Run via browser:

```
http://localhost/your-project-folder
```

---

## 🔍 Challenges & Solutions

**Authentication Issue**

* Password input mismatch with database hash
* ✔ Solved using bcrypt verification

**Notification Bug**

* Wrong message on user action
* ✔ Fixed by separating logic for add vs reset

---

## 📈 Impact

* Simplified employee data management
* Reduced manual work via PDF export
* Improved internal data access

---

## 🚀 Future Improvements

* REST API integration
* Business Intelligence dashboard
* UI modernization (React/Vue)

---

## 👩‍💻 Author

**Your Name**

---

## ⭐ Support

If you find this project useful, feel free to give it a star ⭐
