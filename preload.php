<?
if (get_magic_quotes_gpc() == true) 
{
  foreach($_COOKIE as $key => $value) 
  {
    $_COOKIE[$key] = stripslashes($value);
  }
  foreach($_GET as $key => $value) 
  {
    $_GET[$key] = stripslashes($value);
  }
  foreach($_POST as $key => $value) 
  {
    $_POST[$key] = stripslashes($value);
  }
  foreach($_REQUEST as $key => $value) 
  {
    $_REQUEST[$key] = stripslashes($value);
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

