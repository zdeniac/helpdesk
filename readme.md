# Dokumentáció

## Back-end

## Route-ok
| Endpoint                     | Method | Description                   | Auth             |
| ---------------------------- | ------ | ----------------------------- | ---------------- |
| `/login`                     | POST   | Authenticate user, return JWT | Public           |
| `/logout`                    | POST   | Logout user                   | JWT              |
| `/events`                    | GET    | List user events              | JWT              |
| `/events`                    | POST   | Create new event              | JWT              |
| `/events/{id}`               | PUT    | Update event                  | JWT              |
| `/events/{id}`               | DELETE | Delete event                  | JWT              |
| `/helpdesk`                  | GET    | Get user conversation         | JWT + role:user  |
| `/helpdesk`                  | POST   | Send message                  | JWT + role:user  |