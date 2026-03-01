# Backend Auth (sem banco)
Endpoints:
- POST /backend/auth/register.php
- POST /backend/auth/login.php
- POST /backend/auth/logout.php
- GET  /backend/auth/session_check.php

Usuários: /backend/storage/users.json (com password_hash).
Rate-limit simples por IP: /backend/storage/ratelimit.json
