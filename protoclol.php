<?php
//echo isset($_SERVER['HTTPS'] );

echo  strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
?>