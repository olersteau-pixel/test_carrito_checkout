# API Carrito + checkout

## Descripción del proyecto

Desarrollo de una **cesta de compra (carrito)** que permita a cualquier persona interesada comprar de forma **rápida** y **eficiente** y, a continuación, **completar el proceso de pago** generando una **orden**

## OpenAPI Specification.

A continuación, adjunto las rutas disponibles :
  cart_get - obtener el detalle de un carrito                               GET       /api/cart/{cartId}
  cart_add_item - añadir un producto a un carrito                           POST      /api/cart/{cartId}/items
  cart_remove_item - eliminar un producto de un carrito                     DELETE    /api/cart/{cartId}/items/{productId}
  cart_update_item - actualizar la cantidad de un producto en el carrito    PUT       /api/cart/{cartId}/items/{productId}  
  cart_checkout - validar el carrito y generar el pedido                    POST      /api/cart/{cartId}/checkout

La api via swagger se encuentra disponible en la dirección http://localhost:8080/api/doc
La collection de prueba postman esta disponible igualmente en la carpeta resource

## Modelado del dominio

En ese caso, solo nos hemos centrado en 2 modelos :
- El carrito (**Cart**) que es el modelo fundamental y donde se concentra la mayoria de los casos de uso de ese ejemplo, es donde manejamos las funcionalidades de añadir un producto al carrito, eliminarlo, actualizar su contenido o obtener todo el detalle de un carrito
- El pedido (**Order**), en ese caso, solo usamos el dominio y las capas de infraestruturas de repository y doctrine, el caso de uso se queda en el carrito y en el EndPoint de checkout.

## Tecnología utilizada

- PHP (fpm): 8.2
- XDebug: 3.3
- MySQL: 8.0
- Nginx: lastest

## Instrucciones para levantar el entorno
Se usara los comandos del fichero Makefile para que sea lo mas sencillo posible.

Para levantar el proyecto, es necesario ejecutar el siguiente comando

make up

Para descargarse los paquete del composer, ese otro comando 

make install

Y finalmente, para crear la base de datos, tablas y los contenidos de ejemplos que se usaran para el funcionamiento de la api, ese tercer comando 

make init-db

## Comando para lanzar los tests
Usaremos

make test-unit

para lanzar los tests unitarios sobre los casos de usos. Se ha incluido tambien unos tests sobre la entidad Order.