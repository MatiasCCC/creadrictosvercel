<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"));
    $image = $data->image;

    $to = "correo_del_usuario@example.com"; // Reemplaza con el correo del usuario capturado
    $subject = "Imagen Capturada";
    $message = "Aquí tienes la imagen capturada.";
    $separator = md5(time());
    $eol = "\r\n";

    $headers = "From: tu_email@example.com" . $eol;
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;

    $body = "--" . $separator . $eol;
    $body .= "Content-Type: text/plain; charset=\"iso-8859-1\"" . $eol;
    $body .= "Content-Transfer-Encoding: 7bit" . $eol . $eol;
    $body .= $message . $eol;

    // Adjuntar imagen
    $body .= "--" . $separator . $eol;
    $body .= "Content-Type: image/png; name=\"captura.png\"" . $eol;
    $body .= "Content-Transfer-Encoding: base64" . $eol;
    $body .= "Content-Disposition: attachment" . $eol . $eol;
    $body .= base64_encode(file_get_contents($image)) . $eol;
    $body .= "--" . $separator . "--";

    if (mail($to, $subject, $body, $headers)) {
        echo "Correo enviado exitosamente";
    } else {
        echo "Error al enviar el correo";
    }
}
?>
