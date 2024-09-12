<?php

namespace Drupal\userlist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\File\FileSystemInterface;
use Drupal\userlist\UserData;

class UserListController extends ControllerBase {

  //Dependencias inyectadas
  protected $formBuilder;
  protected $userData;

  //Constructor
  public function __construct(FormBuilderInterface $form_builder, UserData $user_data) {
    $this->formBuilder = $form_builder;
    $this->userData = $user_data;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder'),
      $container->get('userlist.user_data')
    );
  }

  //Metodo que obtiene los usuarios de nuestro servicio (Simulando API)
  private function getUsers() {
    return $this->userData->getUsers();
  }

  //Metodo para mostrar el listado de usuarios
  public function showUserList(Request $request) {

    $users = $this->getUsers(); //Obtener usuarios

    $page = $request->query->get('page', 1); //Obtener pagina actual e intervalo de usuarios
    $items_per_page = 5;
    $offset = ($page - 1) * $items_per_page;

    $filtered_users = array_slice($users, $offset, $items_per_page); //Filtrar usuarios con el intervalo

    $output = '<table class="user-table">'; //Generamos la tabla con una clase para el CSS
    $output .= '<tr><th>Usuario</th><th>Nombre</th><th>Apellido1</th><th>Apellido2</th><th>Email</th></tr>';
    foreach ($filtered_users as $user) {
      $output .= '<tr>';
      $output .= '<td>' . $user['name'] . '</td>';
      $output .= '<td>' . $user['name'] . '</td>';
      $output .= '<td>' . $user['surname1'] . '</td>';
      $output .= '<td>' . $user['surname2'] . '</td>';
      $output .= '<td>' . $user['email'] . '</td>';
      $output .= '</tr>';
    }
    $output .= '</table>';

    $total_users = count($users); //A partir de aqui creamos la paginacion
    $total_pages = ceil($total_users / $items_per_page);

    $pagination = '<div class="pagination">';
    for ($i = 1; $i <= $total_pages; $i++) {
      $pagination .= '<a href="#" class="pagination-link" data-page="' . $i . '">' . $i . '</a>';
    }
    $pagination .= '</div>';

    $form = $this->formBuilder->getForm('Drupal\userlist\Form\UserSearchForm'); //Obtenemos el formulario de busqueda

    // Renderizar contenido completo: formulario, listado y paginacion + nuestra libreria javascript AJAX y css
    return [
      'search_form' => $form, 
      'user_list_wrapper' => [
        '#type' => 'markup',
        '#markup' => '<div id="user-list-wrapper">' . \Drupal::service('renderer')->render($form) . $output . $pagination . '</div>',
      ],
      '#attached' => [
        'library' => [
          'userlist/ajax-pagination',
        ],
      ],
    ];

  }

  //Metodo para manejar la solicitud AJAX de la lista de usuarios
  public function getUserList(Request $request) {

    $name = $request->query->get('name', ''); //Obtenemos los parametros de busqueda
    $surname1 = $request->query->get('surname1', '');
    $surname2 = $request->query->get('surname2', '');
    $email = $request->query->get('email', '');

    $users = $this->getUsers(); //Obtenemos la lista de usuarios

    //Filtrar usuarios segun los criterios de busqueda
    $filtered_users = array_filter($users, function($user) use ($name, $surname1, $surname2, $email) {
      return (empty($name) || stripos($user['name'], $name) !== false) &&
            (empty($surname1) || stripos($user['surname1'], $surname1) !== false) &&
            (empty($surname2) || stripos($user['surname2'], $surname2) !== false) &&
            (empty($email) || stripos($user['email'], $email) !== false);
    });

    $page = $request->query->get('page', 1); //Aqui empieza otra vez la paginacion del intervalo de resultados
    $items_per_page = 5;
    $offset = ($page - 1) * $items_per_page;

    $paginated_users = array_slice($filtered_users, $offset, $items_per_page);

    $output = '<table class="user-table">'; //Tabla con los resultados
    $output .= '<tr><th>Username</th><th>Nombre</th><th>Apellido1</th><th>Apellido2</th><th>Correo Electrónico</th></tr>';
    foreach ($paginated_users as $user) {
      $output .= '<tr>';
      $output .= '<td>' . $user['name'] . '</td>';
      $output .= '<td>' . $user['name'] . '</td>';
      $output .= '<td>' . $user['surname1'] . '</td>';
      $output .= '<td>' . $user['surname2'] . '</td>';
      $output .= '<td>' . $user['email'] . '</td>';
      $output .= '</tr>';
    }
    $output .= '</table>';

    $total_users = count($filtered_users); //Paginacion
    $total_pages = ceil($total_users / $items_per_page);

    $pagination = '<div class="pagination">';
    for ($i = 1; $i <= $total_pages; $i++) {
      $pagination .= '<a href="#" class="pagination-link" data-page="' . $i . '">' . $i . '</a>';
    }
    $pagination .= '</div>';

    //Devolvemos la respuesta com JSON con el HTML actualizado con la nueva lista de usuarios
    return new JsonResponse(['html' => '<div id="user-list-wrapper">' . $output . $pagination . '</div>']);
  }
}
