# Project 3: Database Integration — Task Manager

DecodeLabs Full Stack Development Internship — Batch 2026.

A CRUD app built with **raw PHP (PDO native driver) + MySQL** — no ORM — demonstrating relational schema design, RESTful CRUD mapping, and SQL injection defense.

## Stack
- PHP (PDO) — native driver, not an ORM
- MySQL — relational database
- Vanilla HTML/CSS/JS frontend

## Schema
Two tables in a **1:Many** relationship:
- `users` (`user_id` PK, `email` UNIQUE, `name` NOT NULL)
- `tasks` (`task_id` PK, `user_id` FK → users, `status` CHECK constraint)

See `schema.sql`.

## CRUD → HTTP → SQL mapping
| Action | HTTP Method | SQL |
|---|---|---|
| Create | POST | INSERT |
| Read | GET | SELECT |
| Update | PUT | UPDATE |
| Delete | DELETE | DELETE |

## SQL Injection Defense
All queries use **PDO prepared statements** with `PDO::ATTR_EMULATE_PREPARES => false`, meaning parameters are sent separately from the query itself and can never be interpreted as executable SQL — the classic `' OR 1=1 --` injection is neutralized by design.

## Setup
1. Create the database: `mysql -u root -p < schema.sql`
2. Update credentials in `db.php`
3. Serve with PHP's built-in server: `php -S localhost:8000`
4. Open `http://localhost:8000`

## File Structure
```
├── schema.sql
├── db.php
├── api/
│   ├── users.php
│   └── tasks.php
├── index.html
├── style.css
├── script.js
└── README.md
```
