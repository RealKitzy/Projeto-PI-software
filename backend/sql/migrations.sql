-- MIGRAÇÃO RECOMENDADA (senha segura com bcrypt)
-- Seu schema atual define senha VARCHAR(32), o que NÃO comporta password_hash (~60 chars).
-- Execute isto no Postgres (Railway):

ALTER TABLE usuarios
  ALTER COLUMN senha TYPE VARCHAR(255);

-- Opcional: restringir tipo_usuario
-- ALTER TABLE usuarios
--   ADD CONSTRAINT usuarios_tipo_usuario_chk CHECK (tipo_usuario IN ('catador','empresa'));
