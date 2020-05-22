<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'DBExecutor.php';

class mailer
{
  private $mailer;

  public function __construct()
  {
    try
    {
      $params = parse_ini_file('../params/mail_param.ini');
      $this->mailer = new PHPMailer(true);
      //$this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
      $this->mailer->isSMTP();
      $this->mailer->Host = $params['host'];
      $this->mailer->SMTPAuth = true;
      $this->mailer->Username = $params['username'];
      $this->mailer->Password = $params['password'];
      $this->mailer->SMTPSecure = 'ssl';
      $this->mailer->Port = $params['smtpport'];
      $this->mailer->setFrom($params['sendfrom'], 'Mailer');
    }
    catch(Exception $ex)
    {
      throw $ex;
    }
  }

  public function send(string $to, string $content)
  {
    try
    {
      $this->mailer->addAddress($to, 'Manager');
      $this->mailer->isHTML(true);
      $this->mailer->Subject = 'New request created';
      $this->mailer->Body = $content;

      $this->mailer->send();
    }
    catch(Exception $ex)
    {
      throw $ex;
    }
  }

  public function constructMessage($param)
  {
    ob_start();
    require('message.php');
    $page = ob_get_clean();
    return $page;
  }
}
