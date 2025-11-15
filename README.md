
# Weather API - Laravel

Este proyecto es una API sencilla para almacenar lecturas de clima (temperature, humidity, etc.) usando la API de OpenWeather.

## 📋 Requisitos
- PHP 8.1+ (o la versión compatible con tu proyecto)
- Composer
- MySQL (o cualquier DB compatible configurada en `.env`)
- Node.js + npm (opcional para assets)

## 🔧 Instalación y configuración
1. Clona el repo y entra en la carpeta:
```bash
git clone <repo-url>
cd weather-api
```
2. Instala dependencias PHP:
```bash
composer install
```
3. Copia el archivo de configuración de entorno y actualiza variables:
```bash
cp .env.example .env
```
Edita `.env` y configura al menos:
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (tu conexión a MySQL)
- `APP_URL` (ej.: http://127.0.0.1:8000)
- `OPENWEATHER_API_KEY` (opcional: se usará una clave por defecto como fallback, pero es mejor configurarla aquí)

4. Genera la APP KEY:
```bash
php artisan key:generate
```

5. Ejecuta migraciones para crear tablas (incluida `weathers`):
```bash
php artisan migrate
```

6. (Opcional) Instala dependencias JS y compila si vas a usar assets:
```bash
npm install
npm run build
```

7. Inicia el servidor local:
```bash
php artisan serve
```
Por defecto escuchará en: `http://127.0.0.1:8000`.

## 🛣️ Rutas / Endpoints
- `GET /status` — comprueba que la API está arriba (devuelve un JSON simple)
- `GET /weather` — devuelve todos los registros almacenaros en la DB, ordenados por `created_at` descendente
- `GET /weather/fetch` — obtiene datos actuales de OpenWeather y los almacena en la tabla `weathers`

Parámetros para `/weather/fetch`:
- Puedes usar `?city_name=Manizales&country_code=co` para enviar separado el nombre de ciudad y el código del país.
- O bien usar `?q=Manizales,co` (estilo OpenWeather) — si no pasas parámetros se usará `Manizales,co` por defecto.

## ✨ Ejemplos de uso

### curl
Guardar clima para Manizales, Colombia:
```bash
curl "http://127.0.0.1:8000/weather/fetch?city_name=Manizales&country_code=co"
```

Usar `q`:
```bash
curl "http://127.0.0.1:8000/weather/fetch?q=Manizales,co"
```

Obtener todas las lecturas almacenadas:
```bash
curl "http://127.0.0.1:8000/weather"
```

### Postman / Apidog
1. Crea una nueva colección (o request) en Postman/Apidog.
2. Para `fetch`:
	 - Método: GET
	 - URL: `http://127.0.0.1:8000/weather/fetch`
	 - Query params:
		 - `city_name`: `Manizales`
		 - `country_code`: `co`
	 - Envia la request; deberías recibir el JSON del registro creado con estado 201.
3. Para listar registros:
	 - Método: GET
	 - URL: `http://127.0.0.1:8000/weather`
	 - Devuelve un array JSON con los últimos registros.

### Ejemplo de respuesta (creación) — `GET /weather/fetch`
```json
{
	"id": 1,
	"city": "Manizales",
	"country": "CO",
	"temperature": 14.2,
	"humidity": 75,
	"pressure": 1012,
	"condition": "clear sky",
	"visibility": 10000,
	"collected_at": "2025-11-15T18:00:00Z",
	"created_at": "2025-11-15T18:01:00Z",
	"updated_at": "2025-11-15T18:01:00Z"
}
```

## ✅ Notas y troubleshooting
- Si ves un error de tabla `Base table or view not found: 1146 Table 'weather.weather' doesn't exist` al consultar `/weather` — puede ser por una inconsistencia de nombre de tabla (Eloquent intentó usar `weather` en singular). Asegúrate de:
	- Ejecuar `php artisan migrate` para crear la tabla `weathers`.
	- En el `Weather` model está declarado `protected $table = 'weathers';` (si prefieres `weather` singular, adapta la migración o el modelo).
- Asegúrate de configurar la conexión DB en `.env`.
- Si no aparece `OPENWEATHER_API_KEY`, la aplicación usa una clave por defecto como fallback, pero es recomendable configurar tu propia clave:
	- Consigue una API Key desde https://openweathermap.org
	- Añádela a `.env`:
	```bash
	OPENWEATHER_API_KEY=tu_clave_aqui
	```
---
