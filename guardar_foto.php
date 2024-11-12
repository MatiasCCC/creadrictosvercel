<?php
// Incluir PHPMailer
require '/home/wwwcread/app.creadictos.com.bo/phpmailer/src/PHPMailer.php';
require '/home/wwwcread/app.creadictos.com.bo/phpmailer/src/SMTP.php';
require '/home/wwwcread/app.creadictos.com.bo/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ruta del archivo trigger.json
$triggerFile = __DIR__ . '/trigger.json';

// Leer el estado actual del trigger
$triggerData = json_decode(file_get_contents($triggerFile), true);

// Verificar si el trigger está activado
if (!$triggerData['trigger']) {
    echo "No se ha activado el trigger para tomar una foto.";
    exit;
}

// Desactivar el trigger inmediatamente para evitar múltiples capturas
$triggerData['trigger'] = false;
file_put_contents($triggerFile, json_encode($triggerData));

// Función para obtener el último correo electrónico desde el archivo CSV
function obtenerUltimoEmail($csvFile) {
    $emails = [];
    if (($file = fopen($csvFile, 'r')) !== false) {
        while (($row = fgetcsv($file)) !== false) {
            $emails[] = $row[1];  // El correo electrónico está en la segunda columna
        }
        fclose($file);
    }
    return end($emails);  // Retorna el último correo electrónico
}

// Ruta del archivo CSV
$csvFile = __DIR__ . '/datos.csv';

// Obtener el último correo electrónico
$email = obtenerUltimoEmail($csvFile);

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    // Directorio para guardar las fotos
    $uploadDir = __DIR__ . '/fotos/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    // Ruta del archivo
    $filePath = $uploadDir . 'foto_' . date('Ymd_His') . '.png';
    move_uploaded_file($_FILES['photo']['tmp_name'], $filePath);

    // Configurar PHPMailer para enviar el correo
    $mail = new PHPMailer(true); // Habilita excepciones para manejo de errores

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'mail.creadictos.com.bo';
        $mail->SMTPAuth = false;
        $mail->Username = 'app@creadictos.com.bo';
        $mail->Password = 'creadictos.com.bo';
        $mail->SMTPSecure = '';
        $mail->Port = 587;

        // Configuración del correo
        $mail->setFrom('app@creadictos.com.bo', 'Formulario Web');
        $mail->addAddress($email);
        $mail->Subject = 'Foto capturada';
        $mail->Body = 'Aquí está la foto capturada automáticamente.';

        // Adjuntar la foto
        $mail->addAttachment($filePath);

        // Enviar el correo
        $mail->send();
        echo 'Foto enviada exitosamente al correo: ' . $email;
    } catch (Exception $e) {
        echo 'Error al enviar el correo: ' . $mail->ErrorInfo;
    }
} else {
    echo "Error al guardar la foto.";
}
?>
