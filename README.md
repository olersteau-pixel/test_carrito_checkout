# API Carrito + checkout

## Objetivo

El desarrollo de una **cesta de compra (carrito)** que permita a cualquier persona interesada comprar de forma **rápida** y **eficiente** y, a continuación, **completar el proceso de pago** generando una **orden**

## Tecnología utilizada

- PHP (fpm): 8.2
- XDebug: 3.3
- MySQL: 8.0
- Nginx: lastest

## Arrancar el proyecto
Se usara los comandos del fichero Makefile para que sea lo mas sencillo posible.

Para levantar el proyecto, es necesario ejecutar el siguiente comando

make up

Para descargarse los paquete del composer, ese otro comando 

make install

Y finalmente, para crear la base de datos, tablas y los contenidos de ejemplos que se usaran para el funcionamiento de la api, ese tercer comando 

make init-db