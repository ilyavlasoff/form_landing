<?php

require '../vendor/autoload.php';
require_once 'DBExecutor.php';
require_once 'Mailer.php';

if (! (isset($_REQUEST['username']) && isset($_REQUEST['mail'])
&& isset($_REQUEST['message']) && isset($_REQUEST['phone'])))
{
  http_response_code(400);
  exit();
}

$username = htmlentities(trim($_REQUEST['username']));
$mail = htmlentities(trim($_REQUEST['mail']));
$message = htmlentities(trim($_REQUEST['message']));
$phone = htmlentities(trim($_REQUEST['phone']));

$creationTime = new DateTime();

try {
  $db = new DBExecutor();
  $lastRequestTime = $db->checkLastRequestTime($mail);
  if ($lastRequestTime)
  {
      $lastRequestDateDifference = date_diff(new DateTime(), new DateTime($lastRequestTime));
      $hours = ($lastRequestDateDifference->days*24)+$lastRequestDateDifference->h;
      if ($hours < 1)
      {
          http_response_code(200);
          echo json_encode(['apply' => false, 'remains' => 60 - $lastRequestDateDifference->i], true);
          exit();
      }
  }

  $manager = $db->selectManager();
  $requestId = $db->addRequest($username, $mail, $phone, $message, $creationTime, $manager['id']);

  $mailer = new Mailer();
  $mailContent = $mailer->constructMessage(['username' => $username, 'mail' => $mail,
    'phone' => $phone, 'message' => $message, 'id' => $requestId]);
  $mailer->send($manager['mail'], $mailContent);

  header('Content-Type: application/json');
  http_response_code(200);
  echo json_encode(['requestId' => $requestId, 'apply' => true], true);
}
catch(\Exception $ex)
{
  header('Content-Type: application/json');
  http_response_code(400);
}

