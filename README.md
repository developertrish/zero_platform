# Chronicle — Algorithm-Free Social Platform

A clean, chronological social media platform built with **Laravel 11 + Inertia.js + Vue 3**.
No algorithm, no ranked feed, no engagement scores. Posts appear in the order they were written.

## Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11 (PHP 8.2+) |
| Frontend | Vue 3 (Composition API) |
| Bridge | Inertia.js v1 |
| Styling | Tailwind CSS v3 |
| Routing (client) | Ziggy (named routes in JS) |
| Build | Vite 5 |
| Database | MySQL 8+ / PostgreSQL 14+ |

## Features

- **Chronological feed** — follows + own posts, newest first, always
- **Explore** — all posts on the platform, chronological  
- **Status updates** — rich text up to 5000 characters
- **File uploads** — images, videos, PDFs, docs (up to 10MB, 4 per post)
- **Likes** — optimistic UI via axios, no page reload
- **Comments + replies** — AJAX, reactive, one level of nesting
- **Profile pages** — avatar, bio, location, website, follower/following counts
- **Follow system** — follow users to filter your feed
- **No algorithm** — ever. The code explicitly orders by `created_at DESC`.

## Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Install JS dependencies
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env
# DB_DATABASE=chronicle
# DB_USERNAME=root
# DB_PASSWORD=secret

# 5. Run migrations + seed demo data
php artisan migrate --seed

# 6. Create storage symlink (for uploaded files)
php artisan storage:link

# 7. Build frontend assets
npm run build
# or for development with HMR:
# npm run dev

# 8. Start the server
php artisan serve
```

## Demo credentials (after seeding)

| Field | Value |
|---|---|
| Email | demo@example.com |
| Password | password |

## Project structure

```
app/
  Http/
    Controllers/
      AuthController.php       — login, register, logout (Inertia responses)
      PostController.php       — feed, explore, show, store, destroy
      CommentController.php    — store (JSON), destroy (JSON)
      LikeController.php       — togglePost, toggleComment (JSON)
      FollowController.php     — toggle follow/unfollow
      ProfileController.php    — show, edit, update (Inertia responses)
    Middleware/
      HandleInertiaRequests.php — shares auth.user + flash to all pages
    Requests/
      StorePostRequest.php
      StoreCommentRequest.php
      UpdateProfileRequest.php
  Models/
    User.php        — followers/following relationships, avatarUrl()
    Post.php        — belongsTo user, hasMany comments/attachments/likes
    Comment.php     — parent/replies tree, polymorphic likes
    Like.php        — morphTo (posts + comments)
    Attachment.php  — typeFromMime(), url(), humanSize()
    Follow.php

database/
  migrations/       — users, posts, attachments, comments, likes, follows
  seeders/          — DatabaseSeeder (16 users, posts, likes, follows, comments)
  factories/        — UserFactory, PostFactory

resources/
  js/
    app.js                     — createInertiaApp entry point
    Layouts/
      AppLayout.vue            — nav, toast, compose modal trigger
    Components/
      ComposeModal.vue         — useForm, file previews, forceFormData
      PostCard.vue             — optimistic likes, attachments, delete
      CommentThread.vue        — AJAX comment posting
      CommentItem.vue          — likes, replies, delete
      ProfileHeader.vue        — avatar, stats, follow button
    Pages/
      Auth/
        Login.vue
        Register.vue
      Posts/
        Feed.vue               — chronological feed + who-to-follow sidebar
        Explore.vue            — all posts globally
        Show.vue               — single post permalink
      Profile/
        Show.vue               — user profile + posts
        Edit.vue               — profile settings form
  views/
    app.blade.php              — Inertia root template (only Blade file)

routes/
  web.php                      — all routes; AJAX endpoints return JSON
```

## How "no algorithm" is enforced

In `PostController::index()` and `PostController::explore()`, the query is:

```php
->orderBy('created_at', 'desc')   // strict chronological — no score ever
```

There is no engagement score, no `RAND()`, no ML model, no recency decay function.
The comment in the code is intentional.
