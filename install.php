<?php
$path = isset($argv[1]) ? $argv[1] : '';
if (!$path) die ('can not found phpmyadmin path');

require_once $path . '/libraries/Config.php';

$config = new \PMA\libraries\Config();
$version = $config->get('PMA_VERSION');

$verArr = explode('.', $version);
$verLen = count($verArr);
$verArr[$verLen - 1] = 'x';
$verDir = implode('.', $verArr);

var_dump($path, $verDir);