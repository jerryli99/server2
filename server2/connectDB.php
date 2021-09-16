<?php
$connect = new mysqli('localhost', '', '', '');
if($connect -> connect_error){
    die('Connection failed: '. $connect -> connect_error. "<br>");
}else{
//  echo "<br>DB connected!!! :-0<br>";
}
?>
