<?php

$path = isset($argv[1]) ? $argv[1] : '';

if (!$path) die('can not found phpmyadmin path');

array_shift($argv);
array_shift($argv);

$config = [];
foreach ($argv as $val) {
  if (preg_match('/^--(.+?)=(.+?)$/', $val, $m)) {
    $config[$m[1]] = $m[2];
  }
}
if (!isset($config['version'])) die('can not enter phpmyadmin version');

$verArr = explode('.', $config['version']);
$verDir = implode('.', [$verArr[0], $verArr[1], 'x']);
$curVer = $verArr[0] * 100 + $verArr[1];

if (!file_exists($path . '/' . 'config.inc.php') && !file_exists($path . '/' . 'config.sample.inc.php'))
    die('invalid phpmyadmin path');
if (!file_exists(__DIR__ . '/phpmyadmin/' . $verDir)) {
  die('invalid phpmyadmin version, allow version:' . PHP_EOL . implode(PHP_EOL, array_filter(scandir(__DIR__ . '/phpmyadmin'), function($item) {
    return !preg_match('/^\./', $item);
  })));
}

function copyFile($from, $to) {
  if (is_dir($from)) {
    if (!file_exists($to)) mkdir($to, '0777', true);
    $arr = array_filter(scandir($from), function ($item){
      return !preg_match('/^\./', $item);
    });
    $res = true;
    foreach ($arr as $item) {
      $absolute = $from . '/' . $item;
      $toAbsolute = $to . '/' . $item;
      $res = $res && copyFile($absolute, $toAbsolute);
    }
    return $res;
  } else {
    $dir = dirname($to);
    if (!file_exists($dir)) mkdir ($dir, '0777', true);
    return copy($from, $to);
  }
}

function rmFile($from) {
  if (is_dir($from)) {
    $arr = array_filter(scandir($from), function ($item){
      return !preg_match('/^\./', $item);
    });
    $res = true;
    foreach ($arr as $item) {
      $absolute = $from . '/' . $item;
      $res = $res && rmFile($absolute);
    }
    return rmdir($from);
  } else {
    return unlink($from);
  }
}
