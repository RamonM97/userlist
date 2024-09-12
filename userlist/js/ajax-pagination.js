(function ($, Drupal) {
  Drupal.behaviors.ajaxPagination = { //Manejar los clicks en enlaces de paginacion
    attach: function (context, settings) {
      $('#user-list-wrapper', context).on('click', '.pagination-link', function (e) {
        e.preventDefault(); //Evita que el enlace siga la URL

        var page = $(this).data('page'); //Mediante el atributo data-page recogemos el numero de la pagina
        var url = '/user-list/ajax?page=' + page; //Construimos la URL para la solicitud AJAX

        $.ajax({
          url: url,
          type: 'GET',
          success: function (response) {
            $('#user-list-wrapper').html(response.html);
            Drupal.attachBehaviors($('#user-list-wrapper')[0], settings); //Vuelve a enlazar el ID
          }
        });
      });

      //Manejar el envio del formulario de busqueda
      $('#user-search-form', context).on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var url = $form.attr('action'); //URL del formulario para la solicitud AJAX

        $.ajax({
          url: url,
          type: 'POST',
          data: $form.serialize(),
          success: function (response) {
            $('#user-list-wrapper').html(response.html); //Reemplazar el contenido con el nuevo HTML
            Drupal.attachBehaviors($('#user-list-wrapper')[0], settings); //Vuelve a enlazar el ID
          }
        });
      });
    }
  };
})(jQuery, Drupal);
