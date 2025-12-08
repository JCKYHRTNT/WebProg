# The Boys Marketplace – Laravel Web Programming Project

This repository contains a small e-commerce web application built with **Laravel 11** as part of a **Web Programming** course.

The main Laravel application is located in:
/WebProg/TheBoysMarketplace


Users can browse products, manage a shopping cart, update their account, and admins can manage products, categories, and user roles.

---

## Table of Contents
- [Tech Stack](#tech-stack)
- [Features](#features)
  - [Public / Guest](#public--guest)
  - [Authentication](#authentication)
  - [User Area](#user-area)
  - [Admin Area](#admin-area)
- [Database Design](#database-design)
  - [Tables](#tables)
  - [Relationships](#relationships)
- [Getting Started](#getting-started)
- [Usage](#usage)
- [Course Notes](#course-notes)

---

## Tech Stack
**Backend:**  
- PHP 8.2+  
- Laravel 11  

**Frontend:**  
- Blade templates  
- Bootstrap 5 (CDN)  

**Database:**  
- MySQL / MariaDB  

**Tooling:**  
- Composer  
- Node.js + npm (optional, current UI does not require Vite)  
- Vite 6, Tailwind CSS 3 (configured but not used in production UI)  

---

## Features
### Guest
- View product listing (`/`)
- Search products by name
- Filter products by category
- View product details:
  - Guest: `/products/{id}`
  - User: `/u/{username}/products/{id}`

---

### Authentication
- **Register:** `/register`  
- **Login:** `/login`  
- **Logout:** `/logout`  
- Roles: **user**, **admin**

**URL Slug Behavior:**  
`Str::slug(name)` determines the route username.  
- User pages → `/u/{username}`  
- Admin pages → `/a/{username}`  

**Middleware:**  
- `auth.user` — enforces correct user slug  
- `admin` — enforces admin privileges and slug check  

---

### User Area
**Home:**  
`/u/{username}`  

**Cart:**  
- View cart: `/u/{username}/cart`  
- Add item: `/u/{username}/cart/add/{product}`  
- Update quantity: `/u/{username}/cart/items/{item}/update`  
- Checkout: `/u/{username}/cart/checkout`  
- Stock validation applies on all cart actions  

**Account:**  
- View: `/u/{username}/account`  
- Update account details (requires password confirmation)  
- Delete account (requires password confirmation)  

---

### Admin Area
#### Admin Home
`/a/{username}`

#### Admin Dashboard
`/a/{username}/admin`

Dashboard includes:
- Product count  
- Category count  
- Admin count  
- List of categories  
- Users eligible for promotion  
- Admins eligible for demotion (self-demotion not allowed)

#### Role Management
- Promote user: `POST /a/{username}/admin/promote`
- Demote admin: `POST /a/{username}/admin/demote`

#### Product Management
- View: `GET /a/{username}/products/{product}`
- Create: `POST /a/{username}/products`
- Edit form: `GET /a/{username}/products/{product}/edit`
- Update: `PUT /a/{username}/products/{product}`
- Delete: `DELETE /a/{username}/products/{product}`

#### Category Management
- Create: `POST /a/{username}/categories`
- Edit: `GET /a/{username}/categories/{category}/edit`
- Update: `PUT /a/{username}/categories/{category}`
- Delete: `DELETE /a/{username}/categories/{category}`

#### Admin Account
- View: `/a/{username}/account`
- Update: `POST /a/{username}/account/update`
- Delete: `POST /a/{username}/account/delete`

---

## Database Design
### Tables

**users**  
- id, name, email, password, role, profpic, timestamps

**categories**  
- id, name, timestamps

**products**  
- id, name, price, image, description, quantity, category_id, timestamps

**carts**  
- id, user_id, timestamps

**cart_items**  
- id, cart_id, product_id, quantity, timestamps

---

### Relationships
- **User** → hasOne **Cart**  
- **Cart** → hasMany **CartItem**  
- **CartItem** → belongsTo **Product**  
- **Product** → belongsTo **Category**  

---

## Getting Started

### 1. Clone the repository
git clone https://github.com/JCKYHRTNT/WebProg.git
cd WebProg/TheBoysMarketplace

### 2. Install PHP dependencies
composer install

### 3. Environment Setup
Create a .env file:
APP_NAME="The Boys Marketplace"
APP_ENV=local
APP_KEY=base64:GENERATE_THIS
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=theboysdb
DB_USERNAME=your_username
DB_PASSWORD=your_password

Generate the application key:
php artisan key:generate

Create the database manually in MySQL.

### 4. Run migrations and seeders
php artisan migrate --seed

### 5. Start the Laravel development server
php artisan serve

The application will be available at:
http://127.0.0.1:8000

## Usage
### Admin Login (UserSeeder)
Email: jackyh@gmail.com
Password: jackyh

### Admin routes:
- /a/{slug}
- /a/{slug}/admin

### User Login (UserSeeder)
Email	Password
john12@gmail.com	john
bobbyhuntrix@gmail.com	bobby

### User routes:
- /u/{slug}
- /u/{slug}/cart
- /u/{slug}/account

Slug = Str::slug(name)

## Course Notes
This project demonstrates:
- Laravel MVC
- Middleware for authentication/authorization
- Eloquent ORM relationships
- Migrations and seeders
- Blade templating
- Form validation
- Role-based permissions
- Cart and stock handling system
