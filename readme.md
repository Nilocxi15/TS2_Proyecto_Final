# Configuracion del entorno 

Posterior a la clonación del repositorio, ingresar a la carpeta `/TS2_Proyecto_Final/IngeniaMath`. 

### 1. Dependencias

Para instalar las librerías necesarias para el funcionamiento en local del proyecto ejecutando:

``` sh
composer install 
```

Para instalar lo relacionado a Cloudinary es necesario ejecutar: 
``` sh
composer require cloudinary/cloudinary_php:^2.0
```


### 2. Configuración del entorno local

Copiar el `.env.example` para generar el nuevo `.env`:

```sh
cp .env.example .env
```

Generar la clave de aplicacion:

```sh
php artisan key:generate
```


Para las credenciales de Cloudinary deben agregarse los siguientes campos: 
```sh
CLOUDINARY_CLOUD_NAME=tu_cloud_name
CLOUDINARY_API_KEY=tu_api_key
CLOUDINARY_API_SECRET=tu_api_secret
CLOUDINARY_SECURE=true

CLOUDINARY_URL=cloudinary://<your_api_key>:<your_api_secret>@your_cloud_name
```

### 3. Ejecucion

El servidor local de php se inicia mediante
```sh
php artisan serve
```