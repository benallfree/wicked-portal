<?

$default_config = $config;

$config_fpath = W::$root_fpath."/config/portal.php";
if(file_exists($config_fpath))
{
  require($config_fpath);
}
$config = array_merge($default_config, $config);

$app_fpath = W::$root_fpath."/app";
if(!file_exists($app_fpath))
{
  W::error("App folder must exist at {$app_fpath}");
}
foreach(W::glob($app_fpath.'/*', GLOB_ONLYDIR) as $module_fpath)
{
  $config['modules'][] = $module_fpath;
}

foreach($config['modules'] as $module_name)
{
  W::load($module_name);
}



W::register_filter('window_title', function($title) use ($config) {
  return $title ? $title : $config['app_title'];
});

$request = W::request();
$parts = explode('/',trim($request['path'],'/'));

$try = array(
  array($config['default_module'], $config['default_action']),
);
if(count($parts)>0)
{
  array_unshift($try, 
    array($parts[0], $config['default_action'])
  );
  if(count($parts)>1)
  {
    array_unshift($try,
      array($parts[0], $parts[1])
    );
  }
}

foreach($try as $path_info)
{
  list($module_name, $action_name) = $path_info;
  $module_config = W::module($module_name);
  $content_fnode = $module_config['fpath']."/routes/{$action_name}";
  foreach(W::glob($content_fnode.".*") as $content_fpath)
  {
    $parts = pathinfo($content_fpath);
    if($parts['extension']!='php')
    {
      $content_fpath = W::filter("{$parts['extension']}_to_php", $content_fpath);
      W::dprint($content_fpath);
    }
    break 2;
  }
}

$config = W::module($module_name);
ob_start();
require($content_fpath);
$s = ob_get_clean();

$s = W::filter('header', $s);
$s = W::filter('footer', $s);

echo $s;
