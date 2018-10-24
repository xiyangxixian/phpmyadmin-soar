<?php
include __DIR__ . '/envcheck.php';

if (!file_exists($path . '/soar')) die ('soar has not installed');

$file = array_values(array_filter(scandir($path . '/soar/phpmyadmin/' . $verDir), function($item){
  return !preg_match('/^\./', $item) && !preg_match('/\.bak$/', $item);
}))[0];

$newFile = $path . '/soar/phpmyadmin/' . $verDir . '/' . $file;
if (intval($curVer) < 408) {
  $oldFile = $path . '/libraries/' . $file;
} else {
  $oldFile = $path . '/libraries/classes/Display/' . $file;
}
if (!file_exists($newFile . '.bak')) die('uninstall soar failed, old file: ' . $file . ', is missing');
copyFile($newFile . '.bak', $oldFile);
rmFile("{$path}/soar");

echo 'soar uninstall success' . PHP_EOL;
