<?php
define("MIN_WIDTH",100);
define("MIN_HEIGHT",100);
if($argc<2){
    exit('Не введено расположение изображения');
}
$fileName = $argv[1];
if(is_file($fileName)===false){
    exit("Введенного файла не существует");
}
$fileExt = pathinfo($fileName)['extension'];
$imageResourse;
switch($fileExt){
    case 'png':
        $imageResourse = imagecreatefrompng($fileName);
        break;

    default:
        exit('Данный формат файла не поддерживается');
    break;
}
if($imageResourse===false) exit("Невозможно открыть изображение");
$imgWidth = imagesx($imageResourse);
$imgHeight = imagesy($imageResourse);

if($imgHeight<=$imgWidth){
    $imageResourse = imagescale($imageResourse,MIN_WIDTH,-1, IMG_BICUBIC_FIXED);//масштабирование картинки
}else{
    $w = ($imgWidth/$imgHeight)*MIN_HEIGHT;
    $imageResourse = imagescale($imageResourse,$w,MIN_HEIGHT, IMG_BICUBIC_FIXED);//масштабирование картинки
}
if($imageResourse===false) exit("Ошибка масштабирования изображения");

//СОХРАНЕНИЕ
$isWrite;
switch($fileExt){
    case 'png':
        $isWrite = imagepng($imageResourse,"File.png");
        break;
}
if($isWrite===false){
    exit("Не получилось записать картинку в файл");
}else{
    echo "Скрипт отработан успешно";
}