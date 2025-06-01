<!-- db.php (Database Connection) -->
<?php
$conn = mysqli_connect("localhost", "root", "", "shopdb");
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>