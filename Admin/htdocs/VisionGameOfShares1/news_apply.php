<!DOCTYPE html>
<html lang="en">

<?php
include "classes.inc.php";
include "conn.inc.php";

//leave if admin not logged in
if(!isset($_SESSION['gos_admin']))
{
    header("Location:login.php");
}

if(!isset($_POST['apply_id']) || empty($_POST['apply_id']))
{
    header("Location:admin.php");
}


header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$time = date('r');
echo "data: The server time is: {$time}\n\n";
flush();


$apply_id=$_POST['apply_id'];
echo "<div id='note'>News $apply_id is successfully Applied!!!<a id='close' class='pull-right'>[Close]&nbsp;</a></div>";
header("Location:news_admin.php");

?>