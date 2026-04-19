# Configuracion del entorno 

Posterior a la clonación del repositorio, ingresar a la carpeta `/TS2_Proyecto_Final/IngeniaMath`. 

### 1. Dependencias

Para instalar las librerías necesarias para el funcionamiento en local del proyecto ejecutando:

``` sh
composer install 
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

### 3. Ejecucion

El servidor local de php se inicia mediante
```sh
php artisan serve
```