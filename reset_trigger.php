<?php

// Ruta del archivo trigger.json
$triggerFile = __DIR__ . '/trigger.json';

// Desactivar el trigger
$triggerData = ['trigger' => false];
file_put_contents($triggerFile, json_encode($triggerData));

echo "Trigger desactivado después de tomar la foto.";
?>
