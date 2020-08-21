<?php
require 'vendor/autoload.php';

$db = \App\Database\Instance::get();

if (!isset($_GET['code'])) {
    header("Location: https://leapkreditt.com");
}

$code = $_GET['code'];

$sth = $db->prepare("SELECT * FROM `url_shortener` WHERE `code` = :code LIMIT 1");
$sth->bindParam(':code', $code, PDO::PARAM_STR);
$sth->execute();

if ($shortener = $sth->fetch(PDO::FETCH_OBJ)) {
    $sth = $db->prepare("UPDATE `url_shortener` SET `visits` = `visits` + 1 WHERE `code` = :code");
    $sth->bindParam(':code', $code, PDO::PARAM_STR);
    $sth->execute();
    // Redirect
    header('Location: ' . $shortener->destination);
} else {
    header("Location: https://leapkreditt.com");
}
