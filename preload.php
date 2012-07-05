<?

W::register_filter('module_config', function($old_config, $module_name) {
  $config_fpath = W::$root_fpath."/config/{$module_name}.php";
  $config = array();
  if(file_exists($config_fpath))
  {
    require($config_fpath);
  }
  return array_merge($old_config, $config);
});

