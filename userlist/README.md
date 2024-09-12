#MODULO DRUPAL V.8/9/10 - FILTRADO DE LISTADO DE USUARIOS MEDIANTE AJAX

##DESCRIPCIÓN

-Este módulo para Drupal muestra el listado de usuarios que llega mediante una API simulada, porque no se proporciona en el enunciado. Por lo tanto recogemos los usuarios en formato JSON del archivo user.json y mediante la clase servicio UserData parseamos los datos a vector para una mejor manipulación.

-Posteriormente mediante el controlador UserListController.php mostraremos estos usuarios con una paginación de 5 usuarios por página mediante AJAX (que no recarge la página). Además tenemos un formulario/buscador añadido, construido en UserSearchForm.php que nos permite filtrar por cualquier término (nombre, apellidos, email) y renderizar de nuevo mediante AJAX solo la tabla.

-Todos los scripts están comentados y detallados.

-La URL en donde se visualiza todo esto es .../user-list

-El módulo está listo para instalar mediante descarga y compresión en .zip .tar .tgz .gz .bz2

-A pesar de que lo pide el enunciado y, después de preguntar, llegué a la conclusión de que efectivamente no hace falta adjuntar la BBDD porque no se utiliza. Los datos de los usuarios para la simulación de API están en formato JSON (user.json).

##TODO

-He intentado hacer lo mejor posible esta prueba técnica, intentado demostrar todos mis conocimientos y he conseguido hacer todo, incluido el apartado más complicado "AJAX" correctamente y el formulario, pero al pulsar la paginación después de realizar una búsqueda, esta deja de funcionar y hay que volver manualmente a la raíz .../user-list. Por la rapidez pedida de entregar esta prueba cuanto antes, dejo por desgracia ese detalle sin conseguir por falta de tiempo.

-Tienen más demostraciones en mi portfolio personal: www.ramondev.es (Aplicaciones web, móviles y videojuegos)

