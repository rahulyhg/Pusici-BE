<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'pusici';

$mysqli = new mysqli($host, $user, $pass, $dbName);
$mysqli->query('SET NAMES utf8');
