<?php

namespace Drupal\userlist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\userlist\UserData;
use Symfony\Component\DependencyInjection\ContainerInterface;

//Formulario para buscar usuarios
class UserSearchForm extends FormBase {

  protected $userData; 

  //Constructor
  public function __construct(UserData $user_data) {
    $this->userData = $user_data; //Inyectamos nuestros servicio con la lista de usuarios (simulacion de API)
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('userlist.user_data')
    );
  }

  public function getFormId() {
    return 'user_search_form';
  }

  //Creamos la estructura del formulario
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#attributes']['id'] = 'user-search-form'; //ID del formulario

    $form['search'] = [ //Campo de texto para buscar
      '#type' => 'textfield',
      '#title' => $this->t('Introduce un Nombre, Apellido o Email para buscar'),
      '#required' => FALSE,
    ];

    $form['submit'] = [ //Boton para buscar
      '#type' => 'submit',
      '#value' => $this->t('Buscar'),
      '#ajax' => [
        'callback' => '::searchCallback', //Metodo que maneja la respuesta AJAX
        'wrapper' => 'user-list-wrapper', //ID del contenedor que se actualizara
        'event' => 'click',
      ],
    ];

    return $form;
  }

  //Manejamos la respuesta AJAX del formulario
  public function searchCallback(array &$form, FormStateInterface $form_state) {

    $search = $form_state->getValue('search'); //Recogemos el termino de busqueda

    
    $response = new AjaxResponse(); //Crear un objeto de respuesta AJAX

    
    $users = $this->userData->getUsers(); //Obtener y filtrar los usuarios en base al termino
    $results = array_filter($users, function($user) use ($search) {
      return stripos($user['name'], $search) !== FALSE ||
            stripos($user['surname1'], $search) !== FALSE ||
            stripos($user['surname2'], $search) !== FALSE ||
            stripos($user['email'], $search) !== FALSE;
    });
    
    $page = \Drupal::request()->query->get('page', 1); //Paginar resultados
    $items_per_page = 5;
    $offset = ($page - 1) * $items_per_page;
    $paginated_results = array_slice($results, $offset, $items_per_page);

    $html = '<table class="user-table">'; //Construir la tabla para los resultados
    $html .= '<tr><th>Usuario</th><th>Nombre</th><th>Apellido1</th><th>Apellido2</th><th>Email</th></tr>';
    foreach ($paginated_results as $user) {
      $html .= '<tr>';
      $html .= '<td>' . $user['name'] . '</td>';
      $html .= '<td>' . $user['name'] . '</td>';
      $html .= '<td>' . $user['surname1'] . '</td>';
      $html .= '<td>' . $user['surname2'] . '</td>';
      $html .= '<td>' . $user['email'] . '</td>';
      $html .= '</tr>';
    }
    $html .= '</table>';

    $total_users = count($results); //Paginacion
    $total_pages = ceil($total_users / $items_per_page);
    $pagination = '<div class="pagination">';
    for ($i = 1; $i <= $total_pages; $i++) {
      $pagination .= '<a href="#" class="pagination-link" data-page="' . $i . '">' . $i . '</a>';
    }
    $pagination .= '</div>';

    //Reemplazamos el contenido del contenedor con los resultados filtrados
    $response->addCommand(new ReplaceCommand('#user-list-wrapper', $html . $pagination));

    return $response;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    //Vacio, lo manejamos por AJAX
  }
}
