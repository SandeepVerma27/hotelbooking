# 🏨 Hotel Booking API

A Laravel-based backend for a hotel booking platform with JWT authentication, role-based access control, hotel and room management, image handling, advanced booking logic with overlap prevention, and search filtering.

---

## 🏗️ Project Features

### 👥 User Roles

- **Admin**
  - Full CRUD access to hotels and rooms
- **User**
  - Can browse and search hotels/rooms
  - Can make bookings and view booking history
  - Can cancel bookings

---

## 🏨 Hotel & Room Management (Admin Only)

- **Hotel**
  - Add, update, delete hotels
  - View list and individual hotel details
- **Room**
  - Add, update, delete rooms
  - Assign rooms to hotels
  - Room details: number, type, amenities, price, images

---

## 📦 Booking System (User Only)

- Authenticated booking via JWT
- Prevents overlapping bookings using check-in/out validation
- Allows booking history and cancellation

---

## 🔍 Advanced Search

- Filter by:
  - Hotel name
  - Location
  - Price range
  - Date availability (check-in / check-out)
- Returns only **available rooms**

---

## 🔐 Authentication

Uses **JWT (JSON Web Token)** for secure API access.

### 🔹 Endpoints

| Method | Endpoint       | Description        |
|--------|----------------|--------------------|
| POST   | `/api/register` | Register a new user |
| POST   | `/api/login`    | Authenticate user  |
| GET    | `/api/logout`   | Logout user (token invalidation) |

---

## 🔧 API Endpoints Overview

### 🛑 Admin Routes (Requires JWT + `role:admin`)

#### 🔹 Hotels

| Method | Endpoint                        | Description        |
|--------|----------------------------------|--------------------|
| GET    | `/api/hotels`                   | List all hotels    |
| GET    | `/api/hotels/{hotel}/edit`      | Get hotel for edit |
| POST   | `/api/hotels`                   | Create a hotel     |
| POST   | `/api/hotels/update`            | Update hotel       |
| DELETE | `/api/hotels/{hotel}/delete`    | Delete hotel       |

#### 🔹 Rooms

| Method | Endpoint                        | Description         |
|--------|----------------------------------|---------------------|
| GET    | `/api/rooms`                    | List all rooms      |
| GET    | `/api/rooms/{room}/edit`        | Get room for edit   |
| POST   | `/api/rooms`                    | Create new room(s)  |
| POST   | `/api/rooms/update`             | Update room         |
| PATCH  | `/api/rooms/{room}`             | RESTful room update |
| DELETE | `/api/rooms/{room}`             | Delete room         |

---

### 👤 User Routes (Requires JWT + `role:user`)

#### 🔹 Bookings

| Method | Endpoint                         | Description         |
|--------|----------------------------------|---------------------|
| POST   | `/api/bookings`                 | Book a room         |
| GET    | `/api/bookings/history`         | View booking history|
| DELETE | `/api/bookings/{id}/cancel`     | Cancel booking      |

#### 🔹 Hotel Search

| Method | Endpoint                         | Description                |
|--------|----------------------------------|----------------------------|
| GET    | `/api/hotels/search`            | Search hotels and rooms    |

---

## 🧪 Postman Testing

All routes can be tested using **Postman**:

1. **Set Authorization header**:  
   `Authorization: Bearer {JWT_TOKEN}`

2. **Test Role-Based Routes**:  
   - Use an Admin account for hotel/room routes  
   - Use a User account for bookings/search

---

## 📁 Tech Stack

- Laravel 11
- JWT Auth (e.g., `tymon/jwt-auth`)
- MySQL
- Laravel Middleware for role checking
- Postman for API testing
