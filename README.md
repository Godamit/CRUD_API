# PHP Backend CRUD API

A backend API built using **PHP and MySQL** that performs full **CRUD operations** using HTTP methods and an `operation` parameter.  
The API supports secure user registration, retrieval, update, and deletion with prepared statements and JSON responses.

---

## Features

- User Registration (password hashing)
- Fetch user by ID
- Update user details
- Delete user
- Prepared statements (SQL injection safe)
- JSON responses with proper HTTP status codes

---

## Tech Stack

- Backend: PHP
- Database: MySQL
- Security: password_hash, prepared statements
- API Format: JSON

---

## Database

**Table: `crud`**

```sql
CREATE TABLE crud (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  phone VARCHAR(20),
  role VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


```

API Usage
Register User

POST (operation=1)

{
"operation": "1",
"name": "Amit",
"email": "amit@example.com",
"password": "password123",
"phone": "9999999999",
"role": "user"
}

Get User

GET (operation=2)

/api.php?operation=2&id=1

Update User

PUT (operation=3)

operation=3&id=1&name=Updated&email=updated@example.com&phone=8888888888&role=user

Delete User

POST (operation=4)

{
"operation": "4",
"id": 1
}

Error Codes

400 Bad Request

404 Not Found

405 Method Not Allowed

500 Server Error

Testing

Tested with Postman

Verified full CRUD flow and edge cases

Author

Amit Pandey
GitHub: https://github.com/Godamit

LinkedIn: https://linkedin.com/in/amittpandey
