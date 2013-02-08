<?

class PortalMixin extends Mixin
{
  static function render($portal_config = null)
  {
    W::action('portal_init');
  
    if(!$portal_config)
    {
      $portal_config = W::module('portal');
    }

    $render_mode = self::compute_render_mode(W::request('path'));
    $try = self::compute_paths($portal_config, W::request('path'));

    list($module_name, $content_fpath) = self::compute_final_path($try);

    $s = self::compute_render($render_mode, $module_name, $content_fpath);
    
    echo $s;
  } 

  static function compute_render($render_mode, $module_name, $content_fpath)
  {
    W::action('portal_prerender');
    $vars = W::filter('portal_variables', array());
    extract($vars);
    $config = W::module($module_name);
    ob_start();
    require($content_fpath);
    $s = ob_get_clean();
    switch($render_mode)
    {
      case 'portal':
        $s = W::filter('portal_header', $s);
        $s = W::filter('portal_footer', $s);
        break;
      case 'json':
        header("Content-Type: application/json");
        break;
      case 'js':
        header("Content-Type: application/javascript");
        break;
      case 'xml':
        header("Content-Type: text/xml");
        break;
      case 'rss':
        header("Content-Type: application/rss");
        break;
      case 'html':
        header("Content-Type: text/html");
        break;
      default:
        W::error("Unrecognized render mode {$render_mode}");
    }
    return $s;
  }
    
  static function compute_final_path($try)
  {
    foreach($try as $path_info)
    {
      $module_name = array_shift($path_info);
      $action_name = join("/",$path_info);
      $module_config = W::module($module_name);
      $content_fnode = $module_config['fpath']."/routes/{$action_name}";
      foreach(W::glob($content_fnode.".*") as $content_fpath)
      {
        $parts = pathinfo($content_fpath);
        if($parts['extension']!='php')
        {
          $content_fpath = call_user_func("W::{$parts['extension']}_to_php", $content_fpath);
        }
        break 2;
      }
    }
    
    if(!isset($content_fpath))
    {
      W::dprint($try,false);
      W::error("No path found.");
      
    }
    return array($module_name, $content_fpath);
  }
  
  
  static function compute_render_mode($path)
  {
    $parts = pathinfo($path);
    $mode = 'portal';
    if(isset($parts['extension']))
    {
      $mode = $parts['extension'];
    }
    return $mode;
  }

  static function compute_paths($portal_config, $path)
  {
    $parts = pathinfo($path);
    $path = $parts['dirname'].'/'.$parts['filename'];
    $parts = explode('/',trim($path,'/'));
    $parts = W::array_compact($parts);
    
    $try = array();
    if(count($parts)>0)
    {
      if(count($parts)>1)
      {
        $try[] = $parts;
      }
      $module_config = W::module($parts[0]);
      $try[] = array_merge($parts, array($portal_config['default_action']));
      if(isset($module_config['default_action']))
      {
        $try[] = array_merge($parts, array($module_config['default_action']));
      }
    }
    $try[] = array($portal_config['default_module'], $portal_config['default_action']);
    return $try;
  }
}