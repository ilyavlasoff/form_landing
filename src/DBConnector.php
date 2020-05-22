<?php

class DBConnector
{
  private $pdo;

  public function __construct($host, $port, $db, $username, $password)
  {
    $dsn="pgsql:host={$host};port={$port};dbname={$db};user={$username};password={$password}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
    $this->pdo = new PDO($dsn, $username, $password, $options);
  }

  private function query(string $queryText, array $params)
  {
    $expr = $this->pdo->prepare($queryText);
    $expr->execute($params);
    return $expr;
  }

  public function nonQuery(string $queryText, array $params)
  {
    $result = $this->query($queryText, $params);
    return $result->rowCount();
  }

  public function getRow(string $queryText, array $params)
  {
    $result = $this->query($queryText, $params);
    return $result->fetch();
  }
}
