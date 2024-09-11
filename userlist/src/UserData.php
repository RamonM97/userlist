<?php

namespace Drupal\userlist;

use Drupal\Core\File\FileSystemInterface;

//Clase Servicio que recoge los usuarios en formato JSON y los traduce a vector para mejor manipulacion
class UserData {

  protected $fileSystem;

  public function __construct(FileSystemInterface $file_system) {
    $this->fileSystem = $file_system;
  }

  public function getUsers() {
    $module_path = \Drupal::service('module_handler')->getModule('userlist')->getPath();
    $file_path = $module_path . '/data/users.json';
    if (file_exists($file_path)) {
      $json_content = file_get_contents($file_path);
      return json_decode($json_content, TRUE);
    }
    return [];
  }
}
