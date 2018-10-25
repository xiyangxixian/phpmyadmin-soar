<?php
include __DIR__ . '/envcheck.php';

if (file_exists($path . '/soar')) die ('soar has been installed');

$res = copyFile(__DIR__, $path . '/soar');
if (!$res) die ('copy soar to ' . $path . '/soar failed');

$file = array_values(array_filter(scandir($path . '/soar/phpmyadmin/' . $verDir), function($item){
  return !preg_match('/^\./', $item) && !preg_match('/\.bak$/', $item);
}))[0];

$newFile = $path . '/soar/phpmyadmin/' . $verDir . '/' . $file;
if (intval($curVer) < 408) {
  $oldFile = $path . '/libraries/' . $file;
} else {
  $oldFile = $path . '/libraries/classes/Display/' . $file;
}

copyFile($oldFile, $newFile . '.bak');
copyFile($newFile, $oldFile);
echo 'soar install success' . PHP_EOL;
