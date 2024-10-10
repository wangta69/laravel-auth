<?php
if (!function_exists('configSet')) {
  function configSet($file, $data) {

    foreach($data as $k => $v) {
      config()->set($file.'.'.$k, $v);
    }
    
    $text = '<?php return ' . var_export(config($file), true) . ';';
    // print_r($text);
    file_put_contents(config_path($file.'.php'), $text);
    // \Artisan::call('config:cache'); // 만약 production mode이고 config를 cache 하여 사용하면
  }
}