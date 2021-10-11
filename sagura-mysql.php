<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sagura MySQL Test</title>
  <style>
    body { background-color: ivory; }
  </style>
</head>

<?php

  include('dbconfig.php');
  const myhost = 'localhost';
  const mytable = 'saguratbl';
  
  class mydb {

    public $conn;

    function connect() {
      $this->conn = new mysqli(myhost, username, passwd, calcDB);
      if ($this->conn->connect_error) die($this->conn->error);
    }
    
    function query($sql) {
      if ($this->conn->query($sql) != TRUE) die($this->conn->error);
    }

    function createTbl() {
      $sql = "CREATE TABLE IF NOT EXISTS `" . mytable . "` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `first` TEXT,
        `second` TEXT,
        `operator` TEXT,
        `result` TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
      $this->query($sql);
      echo calcDB . " db and " . mytable . " table connected successfully<br>";
    }
    
    function insRow($first, $second, $operator, $result) {
      $sql = "INSERT INTO `" . mytable . "` (`id`, `first`, `second`, `operator`, `result`) VALUES (NULL, '$first', '$second', '$operator', '$result')";
      $this->query($sql);
      echo "data inserted successfully<br>";
    }
    
    function printRows() {
      $sql = "SELECT * FROM " . mytable;
      $res1 = $this->conn->query($sql);
      echo " <strong>rows num: " . $this->conn->affected_rows . "</strong><br>";
      if ($this->conn->affected_rows > 0) {
        echo '<table border=1 align="center">';
        while ($row = $res1->fetch_array(MYSQLI_BOTH)) {
          echo "<tr><i>";
            echo "<td> " . $row['id'] . "</td>";
            echo "<td> " . $row['first'] . "</td>";
            echo "<td> " . $row['second'] . "</td>";
            echo "<td> " . $row['operator'] . "</td>";
            echo "<td> " . $row['result'] . "</td>";
          echo "</i></tr>";
        }
        echo "</table>";
      }
    }
    
    function dropTable() {
      $sql = "DROP TABLE IF EXISTS `" . mytable . "`";
      if ($this->conn->query($sql) !== TRUE) die($this->conn->error);
      echo "db was cleaned<br>";
    }

    function close() {
      if ($this->conn->close()) echo "db was closed<br>";
      else echo "db failed to close<br>";
    }
  }

  $first = "first2";
  $second = "second3";
  $operator = "operator4";
  $result = "result5";

  function insRow($db, $first, $second, $operator, $result) {
    $sql = "INSERT INTO `" . mytable . "` (`id`, `first`, `second`, `operator`, `result`) VALUES (NULL, '$first', '$second', '$operator', '$result')";
    $db->query($sql);
  }
  function showRows() {
    $db = new mydb;
    $db->connect();
    $db->createTbl();
    $db->printRows();
    $db->close();
  }
  function insRow2($first, $second, $operator, $result) {
    $db = new mydb;
    $db->connect();
    $db->createTbl();
    insRow($db, $first, $second, $operator, $result);
    $db->insRow($first, $second, $operator, $result);
    $db->close();
  }
  function clrRows() {
    $db = new mydb;
    $db->connect();
    $db->createTbl();
    $db->dropTable();
    $db->close();
  }
?>

<body>
  <form align="center" style="background-color: ivory">
    <table border=5 style="background-color:cornsilk; color:indigo;" align="center"><tr><td>
      <h1 style="color:indigo">Sagura MySQL Test</h1>
      <?php include('github.php'); ?>

      <?php
        insRow2($first, $second, $operator, $result);
        showRows();
        clrRows();
        showRows();
      ?>
      </td></tr>
    </table>
  </form>
  <?php include('copyright.php'); ?>
</body>
</html>
