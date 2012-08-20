<?
W::add_mixin('PortalMixin');

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

if($config['should_auto_start'])
{
  W::render($config);
}