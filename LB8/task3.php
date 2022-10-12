<?php
define("OUTPUT_DIR","./ZIP_FILES/");
if($argc<2) exit("Мало параметров. Формат: dir1 dir2 dir3 ... time_in_seconds");
$timeUpload = $argv[$argc-1];//время повторного архивирования
if($timeUpload === (string)(int)$timeUpload){
    $timeUpload = (int)$timeUpload;
}else{
    echo $timeUpload;
    exit("Время указано некорректно (последний параметр)");
}
$dirCount = $argc-2;
$requestDir = [];
$dirNames = [];
for($i=1;$i<$dirCount+1 ;$i++){
    if(is_dir($argv[$i])){
        $requestDir[] = $argv[$i];
        $dirNames[] = pathinfo($argv[$i])['basename'];
    }else{
        $dirName = $argv[$i];
        exit("Директории {$dirName} не существует, либо это не директория");
    }
}

include("../class/FileSystemObject.php");

mkdir(OUTPUT_DIR);
$lastTime = 0;//последнее время обновления
$errorFile = fopen(OUTPUT_DIR . "ErrorFrom_Task3PHP.txt","wt");//файл логирования ошибок

while(true){
    if($lastTime+$timeUpload<time()){//таймер прошел свою границу
        for($i=0;$i<$dirCount;$i++){
            //прочитываем заданную директорию
            $dirFiles=[];
            try{
                $dirFiles = FileSystemObject::readCatalogRecursive($requestDir[$i]);
            }catch(Exception $e){
                fwrite($errorFile,$e->getMessage());
                continue;
            }
            //создаем архив
            $zipName = OUTPUT_DIR . $dirNames[$i] . date("d_m_Y_G_i_s").".zip";
            $zip = new ZipArchive();
            $res = $zip->open($zipName,ZipArchive::OVERWRITE|ZipArchive::CREATE);
            if($res!==true){
                fwrite($errorFile,"Ошибка открытия архива {$zipName}");
                continue;
            }
            //добавление файлов в архив
            foreach($dirFiles as $file){
                if($file->isDir()===false){//обычный файл
                    $fileName = $file->getFullPath();
                    if($zip->addFile($fileName,$file->getFileName())===false){
                        fwrite($errorFile,"Не удалось добавить в архив {$fileName}");
                    }
                }
            }
            $zip->close();
        }
        $lastTime = time();
    }
}