# 🚀 Deploy to Heroku - BACKENDTECHNICALTEST

## Your Live API Will Be:
```
https://backendtechnicaltest.herokuapp.com
```

---

## Deploy Now (Copy & Paste)

Open PowerShell in your project directory and run:

```powershell
heroku login
```

Then:

```powershell
heroku create backendtechnicaltest
$key = php artisan key:generate --show
heroku config:set APP_KEY=$key
heroku addons:create heroku-postgresql:hobby-dev
git add .
git commit -m "Deploy BACKENDTECHNICALTEST to Heroku"
git push heroku main
heroku run php artisan migrate --seed
heroku open
```

**Total time: ~2 minutes**

---

## After Deployment ✅

Your API is live at:
```
https://backendtechnicaltest.herokuapp.com/api/v1
```

### Test It Immediately

**1. Login (get token):**
```bash
curl -X POST https://backendtechnicaltest.herokuapp.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

**2. List Products:**
```bash
curl -H "Authorization: Bearer PASTE_TOKEN_HERE" \
  https://backendtechnicaltest.herokuapp.com/api/v1/products
```

**3. Create Product (admin):**
```bash
curl -X POST https://backendtechnicaltest.herokuapp.com/api/v1/products \
  -H "Authorization: Bearer PASTE_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"title":"New Product","category":"beauty","price":29.99,"stock":50}'
```

---

## Share With Reviewers

**API Base URL:**
```
https://backendtechnicaltest.herokuapp.com
```

**Test Credentials:**
```
Admin:
Email: admin@example.com
Password: password

Regular User:
Email: user@example.com
Password: password
```

**Postman Collection:**
- Use `Backend_API.postman_collection.json` (included in repo)
- Update `base_url` to `https://backendtechnicaltest.herokuapp.com`

---

## Endpoints Available

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/v1/auth/login` | ❌ | Get token |
| GET | `/api/v1/auth/me` | ✅ | Current user |
| POST | `/api/v1/auth/logout` | ✅ | Logout |
| GET | `/api/v1/products` | ✅ | List products |
| POST | `/api/v1/products` | ✅ | Create (admin) |
| GET | `/api/v1/products/{id}` | ✅ | Get product |
| PATCH | `/api/v1/products/{id}` | ✅ | Update (admin/creator) |
| DELETE | `/api/v1/products/{id}` | ✅ | Delete (admin) |
| POST | `/api/v1/products/{id}/thumbnail` | ✅ | Upload thumbnail |

---

## Troubleshooting

**App won't start?**
```powershell
heroku logs --tail --app backendtechnicaltest
```

**Database issues?**
```powershell
heroku run php artisan migrate --seed --app backendtechnicaltest
```

**Reset everything?**
```powershell
heroku pg:reset DATABASE --confirm backendtechnicaltest
heroku run php artisan migrate --seed --app backendtechnicaltest
```

---

## What's Deployed

✅ Laravel API with Sanctum authentication  
✅ PostgreSQL database (free tier)  
✅ Product CRUD operations  
✅ Advanced filtering, sorting, pagination  
✅ Role-based authorization (admin/user)  
✅ File upload support  
✅ 26 automated tests (all passing)  
✅ SSL/HTTPS enabled  

---

## Your URL: 
### 🔗 **https://backendtechnicaltest.herokuapp.com**

**Ready to go live!**
