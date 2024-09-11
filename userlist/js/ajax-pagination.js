(function ($, Drupal) {
  Drupal.behaviors.ajaxPagination = { //Manejar los clicks en enlaces de paginacion
    attach: function (context, settings) {
      $(context).find('.pagination-link').on('click', function (e) {
        e.preventDefault(); //Evitar que el enlace se siga y hacer que se actualice mediante AJAX

        var page = $(this).data('page'); //Mediante el atributo data-page recogemos el numero de la pagina
        var url = '/user-list/ajax?page=' + page; //Construimos la URL para la solicitud AJAX

        $.ajax({
          url: url,
          type: 'GET',
          success: function (response) {
            $('#user-list-wrapper').html(response.html); //Reemplazar el contenido con el nuevo HTML
            //Drupal.attachBehaviors($('#user-list-wrapper')[0], settings);
          }
        });
      });

      // Manejar el envío del formulario de búsqueda
      $('#user-search-form', context).on('submit', function (e) {
        e.preventDefault(); //Evitar que el enlace se siga y hacer que se actualice mediante AJAX

        var $form = $(this); //Referencia al formulario actual
        var url = $form.attr('action'); //URL del formulario para la solicitud AJAX

        $.ajax({
          url: url,
          type: 'POST',
          data: $form.serialize(),
          success: function (response) {
            $('#user-list-wrapper').html(response); //Reemplazar el contenido con el nuevo HTML
            //Drupal.attachBehaviors($('#user-list-wrapper')[0], settings);
          }
        });
      });
    }
  };
})(jQuery, Drupal);
