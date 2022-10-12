<?php
if($argc<2){
    exit('Не введен csv файл');
}
$fileCSV = $argv[1];
$fileHandle = fopen($fileCSV,"rb");
if($fileHandle===false) exit("Невозможно открыть файл");
//к DB
$mysqli = new mysqli("localhost","root","1234","usermailaddresses");
$mysqli->query("SET CHARSET 'UTF8'");
$mysqli->query("DELETE FROM `csvtable`");//очистить старый ввод

$row = fgetcsv($fileHandle);
while($row!==false){
    $name = $row[0];
    $age = $row[1];
    $height = $row[2];
    $weight = $row[3];
    $mysqli->query("INSERT INTO `csvtable` VALUES(\"{$name}\",{$age},{$height},{$weight})");
    $row = fgetcsv($fileHandle);
}