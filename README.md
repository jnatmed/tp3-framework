# Trabajo Practico Nº3 - Persistencia y MVC

OBJETIVO: Construir una aplicación web que utilice un servicio de persistencia externo (SGBD) y
buenas prácticas de código basadas en patrones conocidos, como MVC y OOP.

## Instalación

 - Clonar el repositorio https://github.com/jnatmed/tp3-framework.git
 - Crear un schema de base de datos con algun cliente MySQL
 - Ejecutar los migrations del directorio `sql/` en orden
 - Crear un archivo `config.php` (Hay un ejemplo para copiar en `config.php.example`)
  - Configurar la base de datos creada y los usuarios correspondientes
 - Ejecutar `composer install`

## Deploy / ejecución

### Local

Ejecutar:

```
git clone https://github.com/jnatmed/tp3-framework.git tp3-framework/
cd tp3-framework/
# Pasos de la instalación
php -S localhost:8080
```
Luego ingresar a http://localhost:8888



