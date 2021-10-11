<!--  https://waverleysoftware.com/blog/how-to-write-clean-code/  Comments 
If there ever was a perfect code, it would be the one that doesn’t need any explanations or comments. Clear and comprehensive code with minimum or no comments is far superior to messy code with tons of extra text. Сomments can never make up for bad code, even though in some cases they are necessary. 

Sometimes your code might require a comment, especially when it performs some specific task, or the method intention cannot be understood by its name only. In this case, write a single line and don’t go into too much detail. Also, you may come upon certain cases where some part of the method was turned off for some reason. Then you should leave a comment explaining the reason. Finally, TO-DO comments are an important part of writing good code. They can be very useful when you can’t do something immediately and need to keep it in mind for later. 

But we should always keep in mind that long, verbose comments are often redundant. Excessive comments are unnecessary, especially when they describe an obvious behavior of a method or function. For example, there’s no need to comment on clear and self-explanatory pieces of code. Generally, if the comments don’t bring any useful info or add any value whatsoever,  they should be deleted. And if you’re tempted to write a comment to justify or explain why you made a shady decision, don’t do it. Unfortunately, you won’t make your choice look any better.
-->

<html>
<head>
  <meta charset="utf-8">
  <title>Sagura PHP Calculator</title>
  <style>
    body { background-color: ivory; }
    h1   { color: purple; }
    h2   { color: blue; }
    i    { color: navy; }
  </style>
</head>

<?php 

  const MaxHistory = 5;
  include('dbconfig.php');
  const calcHost = 'localhost';
  const calcTable = 'calctbl';
  
  class calc_db {

    public $conn;

    function connect() {
      $this->conn = new mysqli(calcHost, username, passwd, calcDB);
      if ($this->conn->connect_error) die($this->conn->error);
      return $this->conn;
    }
    
    function query($sql) {
      if ($this->conn->query($sql) != TRUE) die($this->conn->error);
      return TRUE;
    }

    function createTbl() {
      /* $sql = "CREATE DATABASE myDB"; */
      $sql = "CREATE TABLE IF NOT EXISTS `" . calcTable . "` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `first` TEXT,
        `second` TEXT,
        `operator` TEXT,
        `result` TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
      return $this->query($sql);
    }
    
    function insRow($first, $second, $operator, $result) {
      $sql = "INSERT INTO `" . calcTable . "` (`id`, `first`, `second`, `operator`, `result`) VALUES (NULL, '$first', '$second', '$operator', '$result')";
      return $this->query($sql);
    }
    
    function printRows() {
      $sql = "SELECT * FROM " . calcTable;
      $res1 = $this->conn->query($sql);
      $lst = "";
      $i=$this->conn->affected_rows;
      if ($i > MaxHistory) $l1="<tr><td>........................................................</td></tr>";
      else                 $l1="<tr><td>------------------------------------------</td></tr>";
      while ($i-- > MaxHistory) $res1->fetch_array(MYSQLI_BOTH);
      while ($row = $res1->fetch_array(MYSQLI_BOTH)) {
        $l = "<tr>";
/*
          $l = $l . "<td> " . $row['id'] . "</td>";
          $l = $l . "<td> " . $row['first'] . "</td>";
          $l = $l . "<td> " . $row['second'] . "</td>";
          $l = $l . "<td> " . $row['operator'] . "</td>";
*/
          $l = $l . "<td> " . $row['result'] . "</td>";
        $l = $l . "</tr>";
        $lst=$l . $lst;
      }
      return $lst . $l1;
    }
    
    function dropTable() {
      $sql = "DROP TABLE IF EXISTS `" . calcTable . "`";
      if ($this->conn->query($sql) !== TRUE) die($this->conn->error);
      return TRUE;
    }

    function close() {
      return $this->conn->close();
    }
  }

  function showRows() {
    $db = new calc_db;
    $db->connect();
    $db->createTbl();
    $lst = $db->printRows();
    $db->close();
    return $lst;
  }
  
  function insRow2($first, $second, $operator, $result) {
    $db = new calc_db;
    $db->connect();
    $db->createTbl();
    $db->insRow($first, $second, $operator, $result);
    $db->close();
  }
  
  function clrRows() {
    $db = new calc_db;
    $db->connect();
    $db->createTbl();
    $db->dropTable();
    $db->close();
  }

  $first = 2;
  $second = 4;
  $operator = "";
  $result = "";
  $oldRes = "";

  if (isset($_POST['operators'])) {
    $first = doubleval($_POST['first']);
    $second = doubleval($_POST['second']);
    $operator = $_POST["operators"];
    switch ($operator) {
      case "add":
        $result = $first . " + " . $second . " = " . ($first + $second);
        break; 
      case "sub":
        $result = $first . " - " . $second . " = " . ($first - $second);
        break;
      case "mul":
        $result = $first . " * " . $second . " = " . ($first * $second);
        break; 
      case "div":
        $result = $first . " / " . $second . " = " . ($first / $second);
        break;
      case "sin":
        $result = "sin(" . $first . ") = " . sin($first);
        break;
      case "cos":
        $result = "cos(" . $first . ") = " . cos($first);
        break;
      case "tan":
        $result = "tan(" . $first . ") = " . tan($first);
        break;
      case "arc":
        $result = "arctan(" . $first . ") = " . atan($first);
        break;
      case "sqr":
        $result = "sqr(" . $first . ") = " . ($first * $first);
        break;
      case "sqrt":
        $result = "sqrt(" . $first . ") = " . sqrt($first);
        break;
      case "pow":
        $result = "pow(" . $first . ", " . $second . ") = " . pow($first, $second);
        break;
      case "log":
        $result = "log(" . $first . ", " . $second . ") = " . log($first, $second);
        break;
      case "min":
        $result = "min(" . $first . ", " . $second . ") = " . min($first, $second);
        break;
      case "max":
        $result = "max(" . $first . ", " . $second . ") = " . max($first, $second);
        break;
      case "clr":
        $result = "clear";
        clrRows();
        goto l1;
        break;
      default:
        $result = "no action";
        goto l1;
        break;
    }
    insRow2($first, $second, $operator, $result) ;
  }
l1:  
  $oldRes=showRows();

?>

<body><form align="center" method="post" attribute="post">
  <h1>Sagura PHP/MySQL Calculator<br></h1>
  <table style="background-color:cornsilk; color:indigo;" align="center" border=5><tr align="center"><td>
    <p>First Value:<br/><input type="text" name="first" value="<?php echo $first; ?>"></p>
    
    <button type="submit" name="operators" value="add">+</button>
    <button type="submit" name="operators" value="sub">-</button>
    <button type="submit" name="operators" value="mul">x</button>
    <button type="submit" name="operators" value="div">/</button><br><p></p>
    
    <button type="submit" name="operators" value="sin">sin</button>
    <button type="submit" name="operators" value="cos">cos</button>
    <button type="submit" name="operators" value="tan" Hint="Tangent">tan</button>
    <button type="submit" name="operators" value="arc" Hint="Arc tangent">arc</button><br><p></p>

    <button type="submit" name="operators" value="sqr" Hint="Square">sqr</button>
    <button type="submit" name="operators" value="sqrt"Hint="Square root">sqrt</button>
    <button type="submit" name="operators" value="pow" Hint="Power">pow</button>
    <button type="submit" name="operators" value="log" Hint="logarithm">log</button><br><p></p>

    <button type="submit" name="operators" value="min">min</button>
    <button type="submit" name="operators" value="max">max</button>
    <button type="submit" name="operators" value="noact">no</button>
    <button type="submit" name="operators" value="clr" Hint="clear history">Clear</button>
    <p>Second Value:<br/><input type="text" name="second" value="<?php echo $second; ?>"></p>
    <p><strong>
      Result:<br>
      <input type="text" id="result" name="result" readonly="readonly" value="<?php echo $result; ?>">
    </strong></p>
  </td>
  <?php if ($oldRes != "") 
    echo '<td><table style="color:darkgray;">' . $oldRes . '</table></td>'; 
  ?>
  </tr></table>
  </form>
  <?php include('github.php'); ?>
  <?php include('copyright.php'); ?>
</body>
</html>
