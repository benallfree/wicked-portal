<?
if (get_magic_quotes_gpc() == true) 
  {
  // recursively strip slashes from an array
  function stripslashes_r($array) {
    foreach ($array as $key => $value) {
      $array[$key] = is_array($value) ?
        stripslashes_r($value) :
        stripslashes($value);
    }
    return $array;
  }
  
  if (get_magic_quotes_gpc()) {
    $_GET     = stripslashes_r($_GET);
    $_POST    = stripslashes_r($_POST);
    $_COOKIE  = stripslashes_r($_COOKIE);
    $_REQUEST = stripslashes_r($_REQUEST);
  }
}      


W::register_filter('module_config', function($old_config, $module_name) {
  $config_fpath = W::$root_fpath."/config/{$module_name}.php";
  $config = array();
  if(file_exists($config_fpath))
  {
    require($config_fpath);
  }
  return array_merge($old_config, $config);
});

