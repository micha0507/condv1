<?php
if (isset($_POST["btningresar"])){

$Ip = $_SERVER['REMOTE_ADDR'];
$captcha = $_POST['g-recaptcha-response'];
$secretkey = "6Le7gdEqAAAAAACSx6v_u50XJVCimS_fLHqrIGhe";

$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretkey&
response=$captcha&remoteip=$Ip");

$atributos = json_decode($response, true);

$errors = array();
if ($atributos['success'] == false) {
   $errors[] = "Por favor verifica que no eres un robot";

}

}
?>