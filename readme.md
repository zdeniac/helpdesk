# Dokumentáció


## Projekt inicializálás

```bash
cd backend
docker build -t ucc_project_backend .

# Ezután indítsd el a Docker konténereket:
docker-compose up -d

# A futó konténerek ellenőrzéséhez:
docker ps

# Lépj be a backend konténerbe:
docker exec -it backend bash

# Futtasd az Artisan parancsokat:
php artisan

# Migráld az adatbázist:
php artisan migrate

# Seedeld az alapadatokat:
php artisan db:seed

# Tesztek futtatása
php artisan test

# A frontend build-eléséhez és fejlesztői szerver indításához:
cd frontend
npm install
npm run dev
```

## API Route-ok (routes/api.php)
## API Route-ok (routes/api.php)

| Endpoint                          | Method | Description                             |Auth                |
| --------------------------------  | ------ | --------------------------------------  | ------------------ |
| `/password/email`                 | POST   | Send password reset link                | Public             |
| `/password/reset`                 | POST   | Reset user password                     | Public             |
| `/login`                          | POST   | Authenticate user, return JWT           | Public             |
| `/logout`                         | POST   | Logout user                             | JWT                |
| `/me`                             | GET    | Get authenticated user info             | JWT                |
| `/events`                         | GET    | List events                             | JWT                |
| `/events`                         | POST   | Create new event                        | JWT                |
| `/events/{id}`                    | GET    | Show event details                      | JWT                |
| `/events/{id}`                    | PUT    | Update event                            | JWT                |
| `/events/{id}`                    | DELETE | Delete event                            | JWT                |
| `/helpdesk`                       | GET    | Get user conversation                   | JWT + role:user    |
| `/helpdesk`                       | POST   | Send message in conversation            | JWT + role:user    |
| `/agent/conversations`            | GET    | List all conversations for agent        | JWT + role:agent   |
| `/agent/conversations/{id}/close` | POST   | Close a conversation                    | JWT + role:agent   |
| `/agent/helpdesk/{id}`            | GET    | Get conversation by ID                  | JWT + role:agent   |
| `/agent/helpdesk/{id}`            | POST   | Send message as agent in conversation   | JWT + role:agent   |