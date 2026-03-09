# Backend PHP + PostgreSQL (Railway)

## Endpoints
- POST /backend/auth/register.php  (email, senha, perfil)
- POST /backend/auth/login.php     (email, senha)
- POST /backend/auth/logout.php
- GET  /backend/auth/session_check.php

## Perfil / tipo_usuario
O backend aceita:
- perfil: "gerador" ou "catador_cooperativa" (do seu front)
e mapeia para:
- tipo_usuario: "empresa" ou "catador"

Também aceita enviar direto "empresa" / "catador".

## Importante: migração de senha
Execute `backend/sql/migrations.sql` antes de usar, para suportar `password_hash`.
