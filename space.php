<?php
function folderSize ($dir)
{
    $size = 0;
    foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
        $size += is_file($each) ? filesize($each) : folderSize($each);
    }
    return $size;
}



echo foldersize("/var/www/html/voice.baseapi/anyone/");




$df = round(disk_free_space("/var/www/html/voice.baseapi/anyone/") / 1024 / 1024 / 1024);
print("Free space: $df GB");

echo "------------------";

echo (foldersize("/var/www/html/voice.baseapi/")/ 1024 / 1024 / 1024);




$df = round(disk_free_space("/var/www/html/voice.baseapi/") / 1024 / 1024 / 1024);
print("Free space: $df GB");



echo "------------------";

echo foldersize("/var/log/")/ 1024 / 1024 / 1024;

$df = round(disk_free_space("/var/log/") / 1024 / 1024 / 1024);
print("Free space: $df GB");


$df = round(disk_free_space("/var/log/") / 1024 / 1024 / 1024);
print("Free space: $df GB");

?>