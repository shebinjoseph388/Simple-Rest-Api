<?php
$pdo = new PDO('mysql:host=localhost;dbname=posts','root','',array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8",PDO::ATTR_EMULATE_PREPARES=>false));

return $pdo;
