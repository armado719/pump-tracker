# Deploy — Render + Aiven

## 1. Crear la base de datos en Aiven

1. Crea una cuenta en [aiven.io](https://aiven.io) y un servicio **MySQL**.
2. En *Services › tu servicio › Connection Information* copia: host, puerto, base de
   datos, usuario y contraseña.
3. Descarga el **CA Certificate** (mismo panel) y guárdalo como
   `storage/aiven-ca.pem` en el repo, o súbelo como *Secret File* en Render
   (Dashboard → Environment → Secret Files → ruta `/var/www/html/storage/aiven-ca.pem`).
   Aiven exige conexión SSL — sin este certificado la conexión falla.

## 2. Desplegar en Render

1. En Render: **New → Blueprint**, conecta el repo `armado719/pump-tracker`
   y selecciona la rama con el código (la que tenga el `render.yaml`).
2. Render detecta `render.yaml` y crea automáticamente:
   - **pump-tracker** (web service — Nginx + PHP-FPM vía Dockerfile)
   - **pump-tracker-worker** (background worker — `queue:work`)
   - **pump-tracker-scheduler** (cron — `schedule:run` cada minuto, reemplaza
     al crontab que se necesitaría en un VPS)
3. Completa las variables marcadas `sync: false` en el Dashboard de cada
   servicio (APP_KEY, credenciales de Aiven, SMTP, APP_URL).
   - Genera `APP_KEY` localmente: `php artisan key:generate --show`
   - Para no repetir las variables en los 3 servicios, créalas como un
     **Environment Group** (Dashboard → Environment Groups) y vincúlalo a
     los tres.

## 3. Primer despliegue

Render construye la imagen Docker, y `start.sh` ejecuta automáticamente:
- `migrate --force` — crea las tablas en Aiven
- `config:cache`, `route:cache`, `view:cache` — optimiza la app

No necesitas SSH ni cron manual: Render se encarga de reiniciar los
servicios y de ejecutar el scheduler.

## 4. Despliegues posteriores

Cada `git push` a la rama conectada dispara un **deploy automático** en
Render (rebuild de la imagen + restart). No se requiere `deploy.sh`.

## Notas

- Los archivos `deploy/nginx.conf`, `deploy/supervisor.conf` y `deploy/deploy.sh`
  en la carpeta padre son para un **VPS propio** (alternativa). Con Render no
  se usan — Render gestiona el proxy, SSL y los procesos por ti.
- El dominio personalizado se configura en *Settings → Custom Domains* del
  servicio web; Render emite el certificado SSL automáticamente (Let's Encrypt).
