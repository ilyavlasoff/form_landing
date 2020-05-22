<?php

require_once('DBConnector.php');

class DBExecutor
{
  private $conn;

  public function __construct()
  {
    $params = parse_ini_file('../params/db_param.ini');
    $host = $params['host'];
    $port = $params['port'];
    $db = $params['db'];
    $username = $params['username'];
    $password = $params['password'];
    if ($host && $port && $db && $username && $password)
    {
      $this->conn = new DBConnector($host, $port, $db, $username, $password);
    }
    else
    {
      throw new \Exception("Error Processing Request", 1);
    }
  }

  public function  checkLastRequestTime($mail)
  {
    $query = 'SELECT creationTime FROM requests WHERE mail = :mail ORDER BY creationTime DESC LIMIT 1';
    $params = ['mail' => $mail];
    $result = $this->conn->getRow($query, $params);
    return $result['creationtime'];
  }

  public function getRequestById($id)
  {
      $query = 'SELECT * FROM requests WHERE id=:id;';
      $params = ['id' => $id];
      $result = $this->conn->getRow($query, $params);
      return $result;
  }

  public function addRequest($name, $mail, $phone, $message, $creationTime, $manager): int
  {
    $query = 'INSERT INTO requests VALUES (default, :creationTime, :name, :mail, :phone, :message, :manager) RETURNING id;';
    $params = [
      'creationTime' => $creationTime->format('Y-m-d H:i:s'),
      'name' => $name,
      'mail' => $mail,
      'phone' => $phone,
      'message' => $message,
      'manager' => $manager
    ];
    $result = $this->conn->getRow($query, $params);
    return $result['id'];
  }

  public function selectManager()
  {
    $query = 'SELECT manager.id as id, manager.mail FROM manager LEFT JOIN requests ON requests.manager = manager.id GROUP BY manager.id ORDER BY COUNT(requests.id) ASC LIMIT 1';
    $result = $this->conn->getRow($query, []);
    return $result;
  }

  public function getPreviousRequest($id)
  {
    $query = 'SELECT * from requests WHERE id=:id;';
    $params = [
      'id' => $id
    ];
    return $this->conn->getRow($query, $params);
  }
}
