<?php

require 'config.php';

function images_store() {
  global $config;
  
  $images = images_load();
  if ($images === false) {
   $images = array();

   $images_without_local_ids = array_reverse(images_fetch($min_id));
   foreach ($images_without_local_ids as $n => $i) {
     $i['local_id'] = $n + 1;
     $images[$i['local_id']] = $i;
   }
  } else {
    $local_ids = array_keys($images);
    $last_local_id = $local_ids[count($images) - 1];

    foreach(images_fetch($images[$last_local_id]['id']) as $image) {
      $image['local_id'] = ++$last_local_id;
      $images[$image['local_id']] = $image;
    }
  }
    
  ob_start();
    echo "<?php\n\n\$images = ";
    var_export($images);
    echo ';';
    $data_output = ob_get_contents();
  ob_end_clean();

  $fhandle = fopen($config['store'], 'w');
  fwrite($fhandle, $data_output);
  fclose($fhandle);
  
  return $images;
}

function images_fetch($min_id = null) {
  global $config;
  
  $images = array();
  
  $next_url = "https://api.instagram.com/v1/users/{$config['user_id']}/media/recent?access_token={$config['token']}&count=50";
  if ($min_id !== null) $next_url .= "&min_id=$min_id";
  
  while ($next_url) {
    ob_start();
      $ch = curl_init($next_url);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_exec($ch);
      $result = json_decode(ob_get_contents(), true);
    ob_end_clean();
    curl_close($ch);
    
    $images = array_merge($images, $result['data']);
        
    if ($result['pagination'] && $result['pagination']['next_url']) {
      $next_url = $result['pagination']['next_url'];
    } else {
      $next_url = false;      
    }
  }
  
  $images = images_clean($images);
  if ($min_id !== null)
    array_pop($images);

  $images = array_reverse($images);

  return $images;
}

function images_clean($images) {
  foreach($images as &$i) {
    foreach (explode(' ', 'tags type comments likes user user_has_liked') as $key) {
      unset($i[$key]);
    }
    
    if ($i['caption'] && $i['caption']['text']) {
      $i['caption'] = $i['caption']['text'];
    }
  }
  
  return $images;
}

function images_load() {
  global $config;
  
  if (file_exists($config['store'])) {
    include $config['store'];
    return $images;
  } else {
    return false;
  }
}

