# 🌉 BridgeX

**BridgeX** is a web platform designed to connect clients with developers and simplify the process of posting, discovering, and managing software development projects.

Clients can publish projects with their requirements, budget, and expected duration, while developers can browse available projects and submit offers. The platform also provides project management, reviews, and administrative features.

## ✨ Features

### 👤 Client

* Create and manage a client account.
* Post new projects with:

  * Project title and description.
  * Budget.
  * Expected duration.
  * Project requirements.
* View and manage posted projects.
* Receive offers from developers.
* Review submitted offers.
* Accept or reject developer offers.
* Track project status.
* Review and rate developers after project completion.

### 💻 Developer

* Create and manage a developer account.
* Browse available projects.
* View project details and requirements.
* Submit offers for projects.
* Track submitted offers and their status.
* Manage assigned projects.

### 🛡️ Admin

* Access an administrative dashboard.
* Manage platform users.
* Manage projects.
* Manage submitted offers.
* View contact messages and platform-related information.

## 🛠️ Technologies Used

### Frontend

* **HTML**
* **CSS**
* **JavaScript**

### Backend

* **PHP**
* **PHP Sessions**
* **PDO**

### Database

* **MySQL**

### Security

* **Bcrypt password hashing**
* **Prepared database queries using PDO**

## 🗄️ Database

BridgeX uses a **MySQL relational database** to manage the platform's data.

The main database tables include:

* `users` — Stores client, developer, and administrator accounts.
* `projects` — Stores projects posted by clients.
* `offers` — Stores offers submitted by developers.
* `messages` — Stores contact messages.
* `reviews` — Stores developer reviews and ratings.

These tables support the relationships between users, projects, offers, and reviews throughout the platform.

## 🔄 Platform Workflow

1. A user creates an account and selects the appropriate role.
2. A client creates and publishes a project.
3. Developers browse available projects.
4. A developer submits an offer for a project.
5. The client reviews the received offers.
6. The client accepts or rejects an offer.
7. The selected developer works on the assigned project.
8. The project's status is updated throughout the process.
9. After completion, the client can review and rate the developer.

## 📁 Project Structure

```text id="54w89f"
bridgex_wep_platform
│
├── admin/
│   └── Administrative dashboard and management pages
│
├── client/
│   └── Client-side project and offer management
│
├── developer/
│   └── Developer project and offer pages
│
├── database/
│   └── Database configuration and SQL files
│
├── css/
│   └── Application styles
│
├── js/
│   └── JavaScript files
│
├── images/
│   └── Website images and assets
│
├── index.php
├── login.php
└── register.php
```

## 🚀 Getting Started

### Requirements

To run BridgeX locally, you need:

* A local web server such as **XAMPP**
* **PHP**
* **MySQL**
* A modern web browser

### Installation

1. Clone the repository:

```bash id="gw4mrv"
git clone https://github.com/l7unx/bridgex_wep_platform.git
```

2. Move the project folder to your local web server directory.

For XAMPP:

```text id="aqzzk2"
xampp/htdocs/
```

3. Start **Apache** and **MySQL** from the XAMPP Control Panel.

4. Open **phpMyAdmin**.

5. Create the required database and import the SQL file included in the project's `database` folder.

6. Verify the database connection settings in the project configuration.

7. Open the application through your local server in a web browser.

Example:

```text id="90plqf"
http://localhost/bridgex_wep_platform/
```

## 🎓 Academic Project

**BridgeX** was developed as a team project for the **Web Applications** course.

The project aimed to apply web development concepts by building a complete web platform that combines frontend development, server-side programming, database management, user roles, and project-based interactions.

## 👥 Team Project

BridgeX was developed collaboratively as a team project.

### My Contribution

My main contributions to the project included:

-  Designing and developing the complete **Client section** of the platform.
-  Implementing the client-side workflow for interacting with projects and developer offers.
-  Contributing to the user interface and overall client experience.
-  Designing and implementing the slider.
-  Making additional minor contributions and adjustments to other parts of the platform during development.


