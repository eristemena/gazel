<?php
$host = "192.168.1.5";
$user = "dbadminarts";
$pass = "ngneer2008";
$db = "gazel3";
$table = "admingroup";
$connect = mysql_connect($host,$user,$pass) or die("Gagal koneksi");
$pilih_db = mysql_select_db($db) or die("Database tidak ada");
?>