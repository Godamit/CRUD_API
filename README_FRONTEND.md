# CRUD API Frontend

A modern, user-friendly web interface for interacting with the CRUD API backend. Built with pure HTML, CSS, and JavaScript.

## 📋 Features

- ✅ **Register New Users** - Create user accounts with validation
- 🔍 **Retrieve User Info** - Look up user details by ID
- ✏️ **Update User Data** - Modify existing user information
- 🗑️ **Delete Users** - Remove user accounts with confirmation
- 🎨 **Beautiful UI** - Modern, responsive design with gradient styling
- ⚡ **Real-time Validation** - Client-side form validation
- 📱 **Mobile Responsive** - Works on all screen sizes

## 🚀 Setup Instructions

### Prerequisites

- A web server (Apache, Nginx, or any PHP-capable server)
- The CRUD API backend (`crud.php`) running on `localhost`
- PHP with MySQLi extension
- MySQL database with the `crud` table

### 1. Set Up the Backend

Make sure your MySQL database is running with the following structure:

```sql
CREATE DATABASE crud;

CREATE TABLE crud (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role VARCHAR(50) DEFAULT 'user'
);
```

Update the database credentials in `crud.php`:

```php
$host = "localhost";
$password = "786786";      // Your MySQL password
$username = "root";         // Your MySQL username
$database = "crud";         // Your database name
```

### 2. Deploy the Frontend

Copy all three files to your web server directory:

- `index.html` - Main HTML file
- `styles.css` - Stylesheet
- `script.js` - JavaScript logic
- `crud.php` - Backend API

If using Apache with a document root at `/var/www/html/`, place them in `/var/www/html/CRUD_API/`

### 3. Start Your Web Server

**For Apache:**

```bash
sudo systemctl start apache2
```

**For PHP Built-in Server:**

```bash
cd /path/to/CRUD_API
php -S localhost:8000
```

### 4. Access the Frontend

Open your browser and navigate to:

- Local server: `http://localhost/CRUD_API/index.html`
- PHP server: `http://localhost:8000/index.html`

## 📖 Usage Guide

### Register a New User

1. Click the **Register** tab
2. Fill in all required fields:
   - Full Name
   - Email (must be unique)
   - Password (min 6 characters)
   - Phone (min 10 digits)
   - Role (User, Admin, or Moderator)
3. Click **Register** button
4. You'll see a success message if registration succeeds

### Retrieve User Information

1. Click the **Retrieve User** tab
2. Enter the User ID
3. Click **Search**
4. User details will be displayed (if user exists)

### Update User Information

1. Click the **Update User** tab
2. Enter the User ID of the user to update
3. Modify the user's information:
   - Full Name
   - Email
   - Phone
   - Role
4. Click **Update** button
5. You'll see a success message if update succeeds

### Delete a User

1. Click the **Delete User** tab
2. Enter the User ID to delete
3. Click **Delete User** button
4. Confirm the deletion in the popup dialog
5. User will be deleted from the database

## 🔧 API Endpoints Reference

The frontend communicates with these backend operations:

### Operation 1: Register (POST)

```javascript
{
    "operation": "1",
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "phone": "1234567890",
    "role": "user"
}
```

### Operation 2: Retrieve (GET)

```
GET /crud.php?operation=2&id=1
```

### Operation 3: Update (PUT)

```
PUT /crud.php
Body: operation=3&id=1&name=Jane&email=jane@example.com&phone=9876543210&role=admin
```

### Operation 4: Delete (POST)

```javascript
{
    "operation": "4",
    "id": 1
}
```

## 🔒 Security Features

- ✅ Input validation (email, phone, password length)
- ✅ XSS prevention (HTML escaping)
- ✅ Password hashing in backend (BCRYPT)
- ✅ Delete confirmation dialog
- ⚠️ Note: Add CORS headers to backend for production use

## 🎯 Validation Rules

- **Email**: Must be a valid email format
- **Phone**: Minimum 10 digits (spaces and hyphens allowed)
- **Password**: Minimum 6 characters
- **Name/Email/Phone**: Required fields
- **Unique Email**: Cannot register with existing email

## 🌐 CORS Configuration (For Production)

If accessing from a different domain, add CORS headers to `crud.php`:

```php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
```

## 🎨 Customization

### Change API URL

Edit `script.js` line 1:

```javascript
const API_URL = "http://yourdomain.com/path/to/crud.php";
```

### Change Colors

Edit `styles.css` root variables:

```css
:root {
  --primary-color: #6366f1;
  --secondary-color: #8b5cf6;
  /* ... other colors ... */
}
```

## ❌ Troubleshooting

### API Connection Failed

- Ensure `crud.php` is accessible at the configured URL
- Check browser console for CORS errors
- Verify MySQL server is running
- Check database credentials in `crud.php`

### Validation Errors

- Email: Must follow `user@example.com` format
- Phone: Must have at least 10 digits
- Password: Must be at least 6 characters
- All fields: Cannot be empty

### Database Errors

- Verify the `crud` table exists
- Check MySQL user has proper permissions
- Ensure data types match schema

## 📝 License

This project is open source and available for personal and commercial use.

## 👨‍💻 Support

For issues or questions:

1. Check the browser console for errors (F12)
2. Verify backend API is running
3. Check database connection in `crud.php`
4. Review the validation rules above

---

**Happy coding! 🚀**
