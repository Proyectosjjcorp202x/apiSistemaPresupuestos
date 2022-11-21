<?php

if (isset($_POST['base64String'])) {
    $base_a_php = explode(',', $_POST['base64String']);
    $datos = base64_decode($base_a_php[1]);
    file_put_contents('grafica_generada.jpg', $datos);
}