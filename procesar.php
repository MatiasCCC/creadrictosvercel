<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? 'Sin nombre';
    $email = $_POST['email'] ?? 'Sin correo';

    $filePath = __DIR__ . '/datos.csv';

    if (($file = fopen($filePath, 'a')) !== false) {
        fputcsv($file, [$name, $email]);
        fclose($file);

        // Actualiza el archivo trigger.json para notificar a la PC
        file_put_contents(__DIR__ . '/trigger.json', json_encode(['trigger' => true]));

        echo "Datos guardados y notificaci¨®n enviada.";
    } else {
        echo "Error al abrir el archivo CSV.";
    }
} else {
    echo "Acceso no permitido.";
}
?>
