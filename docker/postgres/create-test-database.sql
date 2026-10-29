-- Runs once, the first time the postgres container starts with an empty volume.
-- Tests use their own database so running them never touches your sample data.
CREATE DATABASE dutyboard_test;
