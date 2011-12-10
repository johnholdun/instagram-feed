<?php

$root = $_SERVER['SCRIPT_NAME'];
$filename = array_pop(explode('/', $root));
$root = str_replace('/' . $filename, '', $root);

require 'images.php';
require 'helpers.php';

$images = images_load();
if ($images === false) $images = images_store();

function render($template = 'list', $params = array()) {
  global $images, $root;
    
  $permalink = ($template != 'list');
    
  switch($template) {
    case 'list':
      $count = 20;
      $pages = ceil(count($images) / $count);

      $page = intval($params['page']);
      if ($page < 1) $page = 1;
      if ($page > $pages) $page = $pages;

      $page_images = array_slice(array_reverse($images), ($page - 1) * $count, $count);
      
      break;
      
    case 'permalink':
      $page_images = array($images[intval($params['local_id'])]);
      break;
  }

  date_default_timezone_set('America/New_York');
  
  include 'template.phtml';
}

function render_to_string() {
  global $images;
  
  ob_start();
    $args = func_get_args();
    call_user_func_array('render', $args);
    $output = ob_get_contents();
  ob_end_clean();
  return $output;
}

function action_show() {
  if ($_GET['id'])
    render('permalink', array('local_id' => intval($_GET['id'])));
  else
    render('list', array('page' => intval($_GET['page'])));
}

function action_update() {
  global $images;
  $new_images = images_store();
  
  if (!file_exists('index.html') || count($images) != count($new_images)) {
    $images = $new_images;
    $fhandle = fopen('index.html', 'w');
    fwrite($fhandle, render_to_string());
    fclose($fhandle);
  }
}

$params = array_merge($_GET, $_POST);

if (!isset($params['action']) || !function_exists("action_{$params['action']}"))
  $params['action'] = 'show';

call_user_func("action_{$params['action']}");