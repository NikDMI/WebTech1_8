<?php
define("MAIL_FROM",'nikmikhnovets@yandex.ru');

include("Mail.php");

//подключение к БД
$mysqli = new mysqli("localhost","root","1234","usermailaddresses");
$mysqli->query("SET CHARSET 'UTF8'");
$queryResult = $mysqli->query("SELECT * FROM `useraddresses`");//запрос всех сохраненных адресов
$userCount = $queryResult->num_rows;
if($userCount==0){ 
    $queryResult->free();
    exit("В базе не найдены адреса отправки");
}

//настройка SMTP-формата отправки почтовых сообщений
$config = [
    'defaultFrom' => MAIL_FROM,
    'onError'     => function($error, $message, $transport) {},
    'afterSend'   => function($text, $message, $layer) { echo $text; },
    'transports'  => [
        // Сохранение всех писем в папке
        ['file', 'dir'  => __DIR__ .'/mails'],
        
        // Отправка писем через Yandex, используя SSL и авторизацию
        ['smtp', 'host' => 'smtp.yandex.ru', 'ssl' => true, 'port' => '465', 'login' => MAIL_FROM, 'password' => 'eupbydcsjsdalewp'],
    ],
];

Mailer()->init($config);

$message = Mailer()->newHtmlMessage();

$message->setSubject("Test Email Message PHP");
$message->setSenderEmail(MAIL_FROM);
for($i=0;$i<$userCount;$i++){
    $userInfo = $queryResult->fetch_assoc();
    $message->addRecipient($userInfo['address']);
}
$message->addContent("Your order was proccesed by our Admin. Thank you for your choise!!!");
$message->addContent(file_get_contents('../html/mail.html'));

$message->addRelatedFile('../html/1.png');

Mailer()->sendMessage($message);