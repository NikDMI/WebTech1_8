<?php

if($argc<2) exit("Мало параметров. Формат: url1 url2 ... time_in_seconds");
$timeUpload = $argv[$argc-1];//время повторного архивирования
if($timeUpload === (string)(int)$timeUpload){
    $timeUpload = (int)$timeUpload;
}else{
    echo $timeUpload;
    exit("Время указано некорректно (последний параметр)");
}
$urlCount = $argc-2;
$requestUrl = [];
for($i=1;$i<$urlCount+1 ;$i++){
    $requestUrl[] = $argv[$i];
}


$lastTime = 0;//последнее время обновления
$logFile = fopen("LogFile_Task4PHP.txt","wt");//файл логирования

while(true){
    if($lastTime+$timeUpload<time()){//таймер прошел свою границу
        for($i=0;$i<$urlCount;$i++){
            $url = $requestUrl[$i];
            $crequest = curl_init();
            curl_setopt($crequest, CURLOPT_HEADER, 0);
            curl_setopt($crequest, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($crequest, CURLOPT_URL, "{$url}");
            curl_setopt($crequest, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($crequest, CURLOPT_VERBOSE, 0);
            curl_setopt($crequest, CURLOPT_SSL_VERIFYPEER, false);
            //curl_setopt($crequest, CURLOPT_CONNECTTIMEOUT, 1); 
            //curl_setopt($crequest, CURLOPT_CONNECTTIMEOUT_MS, 1000); 
            //curl_setopt($crequest,CURLOPT_TIMEOUT,1);
            //curl_setopt($crequest,CURLOPT_TIMEOUT_MS,1000);
            curl_setopt($crequest, CURLOPT_NOSIGNAL, 1);
            curl_setopt($crequest, CURLOPT_TIMEOUT_MS, 1000);
            $serverResponse = curl_exec($crequest);
            
            $currTime = date("d.m.Y_G:i:s");
            if($serverResponse===false){//нет ответа от сервера
                $logText = "Сайт {$url} на время {$currTime} является НЕ активным\n";
            }else{
                $logText = "Сайт {$url} на время {$currTime} является активным\n";
            }
            fwrite($logFile,$logText);
            echo $logText;
            curl_close($crequest);
        }
        $lastTime = time();
    }
}