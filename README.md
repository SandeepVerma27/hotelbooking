# Hotel Booking API

A Laravel-based backend for a hotel booking platform with user authentication, role-based access, hotel and room management, image upload, advanced booking logic, and search filtering capabilities.

---

## 🏗️ Project Features

### 👥 User Roles
- **Admin**
  - Full CRUD access to hotels, rooms, and bookings
  - Can manage room images and availability
- **User**
  - Can browse and search for available hotels and rooms
  - Can make bookings, view booking history

### 🏨 Hotel & Room Management
- Add, update, and delete hotels
- Assign rooms to hotels with:
  - Room number, type, amenities, price, etc.
  - Multiple images per room

### 📦 Booking System
- JWT-based authentication
- Prevents overlapping bookings using date validation
- Booking confirmation and cancellation support

### 🔍 Advanced Search
- Filter by location, price range, and availability
- Check-in and Check-out availability logic
- Returns only unbooked rooms

---

## 🔐 Authentication

Uses JWT (JSON Web Token) for secure API authentication.

### Endpoints:
- `POST /api/register`
- `POST /api/login`
- `GET /api/user` (Authenticated)
- `POST /api/logout` (Authenticated)

---

## 🔧 API Endpoints Overview

| Functionality      | Method | Endpoint                      |
|--------------------|--------|-------------------------------|
| Register           | POST   | /api/register                 |
| Login              | POST   | /api/login                    |
| Hotels CRUD (Admin)| REST   | /api/hotels                   |
| Rooms CRUD (Admin) | REST   | /api/rooms                    |
| Book Room          | POST   | /api/bookings                 |
| View Bookings      | GET    | /api/bookings                 |
| Booking History    | GET    | /api/bookings/history         |
| Search             | GET    | /api/search?params            |

---

## 🧪 Postman Testing

All endpoints can be tested using Postman.

- Set Authorization header:
