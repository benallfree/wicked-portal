<?

W::load('debug');
W::load('coolbook');

$app_fpath = W::$root_fpath."/app";
if(!file_exists($app_fpath))
{
  W::error("App folder must exist at {$app_fpath}");
}
foreach(W::glob($app_fpath.'/*', GLOB_ONLYDIR) as $module_fpath)
{
  W::load($module_fpath);
}


$default_config = $config;

$config_fpath = W::$root_fpath."/config/portal.php";
@include($config_fpath);
$config = array_merge($default_config, $config);

W::register_filter('window_title', function($title) use ($config) {
  return $title ? $title : $config['app_title'];
});

$module_name = $config['default_module'];

$content_fpath = W::$root_fpath."/app/{$module_name}/routes/{$config['default_route']}.php";
$config = W::$modules[$module_name];
ob_start();
require($content_fpath);
$s = ob_get_clean();



$s = W::do_filter('header', $s);
$s = W::do_filter('footer', $s);

echo $s;
