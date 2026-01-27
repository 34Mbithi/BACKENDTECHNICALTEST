# Heroku Deployment - Quick Start

## Prerequisites (1 minute)

1. **Create free Heroku account** → https://signup.heroku.com/
2. **Install Heroku CLI** → https://devcenter.heroku.com/articles/heroku-cli
3. **Verify installation:**
   ```powershell
   heroku --version
   ```

---

## Deployment Steps (5 minutes)

### Step 1: Login to Heroku

```powershell
heroku login
```
Opens browser for authentication. Confirm in terminal.

### Step 2: Create Heroku App

```powershell
heroku create your-api-name
```

Replace `your-api-name` with something unique (e.g., `my-product-api-2026`).

**Output:**
```
Creating ⬢ my-product-api-2026... done
https://my-product-api-2026.herokuapp.com/ | https://git.heroku.com/my-product-api-2026.git
```

### Step 3: Set APP_KEY

```powershell
$key = php artisan key:generate --show
heroku config:set APP_KEY=$key
```

### Step 4: Configure Database

Heroku provides free PostgreSQL. Add it:

```powershell
heroku addons:create heroku-postgresql:hobby-dev
```

### Step 5: Update .env for Heroku

Create `config/database.php` adjustment or let Heroku auto-detect. Heroku sets `DATABASE_URL` automatically.

Verify DATABASE_URL was added:
```powershell
heroku config
```

### Step 6: Deploy to Heroku

```powershell
git add .
git commit -m "Ready for Heroku deployment"
git push heroku main
```

**Watch the deployment logs in terminal.**

### Step 7: Run Migrations

```powershell
heroku run php artisan migrate --seed
```

Creates tables and seeds 30 test products + 5 users.

### Step 8: Link Storage (for thumbnails)

```powershell
heroku run php artisan storage:link
```

---

## Testing Your Deployment

### Get Your URL

```powershell
heroku open
```

Or visit: `https://your-api-name.herokuapp.com`

### Test Endpoints

#### 1. **Login** (get token)
```bash
curl -X POST https://your-api-name.herokuapp.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

Save the `token` from response.

#### 2. **List Products**
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  https://your-api-name.herokuapp.com/api/v1/products
```

#### 3. **Create Product** (admin)
```bash
curl -X POST https://your-api-name.herokuapp.com/api/v1/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"title":"Test","category":"beauty","price":19.99,"stock":50}'
```

---

## Useful Heroku Commands

```powershell
# View logs
heroku logs --tail

# View app config
heroku config

# Scale dynos (not needed for free tier)
heroku ps:scale web=1

# Open app in browser
heroku open

# Destroy app (careful!)
heroku apps:destroy --app your-api-name
```

---

## Troubleshooting

### App crashes on deploy?
```powershell
heroku logs --tail
```
Look for errors; common issues:
- Missing APP_KEY → `heroku config:set APP_KEY=...`
- Database not migrated → `heroku run php artisan migrate --seed`
- Storage not linked → `heroku run php artisan storage:link`

### Database errors?
```powershell
# Check PostgreSQL is connected
heroku config:get DATABASE_URL

# Reset database
heroku pg:reset DATABASE --confirm your-api-name
heroku run php artisan migrate --seed
```

### Thumbnail upload fails?
Heroku has ephemeral storage. File uploads work but files are lost on dyno restart. For persistent storage, add **AWS S3**:

```powershell
# Add S3 addon
heroku addons:create bucketeer --as AWS_S3
```

---

## Sharing Your API

Your live API URL format:
```
https://your-api-name.herokuapp.com/api/v1
```

**Share these with reviewers:**
1. Base URL: `https://your-api-name.herokuapp.com`
2. Postman collection: `Backend_API.postman_collection.json`
3. Test credentials:
   - Admin: `admin@example.com` / `password`
   - User: `user@example.com` / `password`

---

## Next Steps

After successful deployment:

1. ✅ Test all endpoints via Postman or curl
2. ✅ Share URL with reviewers
3. ✅ Monitor logs: `heroku logs --tail`
4. ✅ Keep free dyno running (sleeps after 30 mins inactivity; wakes on request)
5. ✅ (Optional) Upgrade to paid dyno for always-on

---

## Cost

- **Free tier**: ✅ $0/month (perfect for demo/testing)
- **Hobby dyno**: $7/month (recommended for production)
- **Standard dyno**: $25+/month (high traffic)

Free tier sleeps after 30 mins of inactivity but is perfect for technical test submission!

---

## Ready?

Run these commands in order:

```powershell
heroku login
heroku create your-unique-app-name
$key = php artisan key:generate --show
heroku config:set APP_KEY=$key
heroku addons:create heroku-postgresql:hobby-dev
git add .
git commit -m "Deploy to Heroku"
git push heroku main
heroku run php artisan migrate --seed
heroku open
```

**Your API will be live in ~2 minutes!**
