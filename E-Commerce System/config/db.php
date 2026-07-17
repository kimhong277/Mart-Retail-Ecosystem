<?php
$conn = mysqli_connect("localhost", "root", "", "e_commerce_system");
if (!$conn) {
    die("Error: " . mysqli_connect_error());
}
