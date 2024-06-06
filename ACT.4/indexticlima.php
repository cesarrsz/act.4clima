<?php
// URL del servicio web de Banxico para obtener tipos de cambio
$apiKey = '9d2a632ce48795f562ebcca1bea4dab329586b71941c6231f017b2bca393eaa9';
$serieId = 'SF43718'; // Reemplaza con el ID de la serie que necesitas
$banxicoApiUrl = "https://www.banxico.org.mx/SieAPIRest/service/v1/series/$serieId/datos/oportuno";

// Configurar opciones para la solicitud HTTP
$options = [
    'http' => [
        'header' => 'Bmx-Token: ' . $apiKey
    ]
];
$context = stream_context_create($options);

// Realizar la solicitud HTTP y decodificar la respuesta JSON para Banxico
$banxicoResponse = @file_get_contents($banxicoApiUrl, false, $context);
if ($banxicoResponse === FALSE) {
    $exchangeRates = null;
    echo "Error al obtener los tipos de cambio de Banxico.";
} else {
    $banxicoData = json_decode($banxicoResponse, true);
    // Verificar si la solicitud a Banxico fue exitosa
    if ($banxicoData !== null && isset($banxicoData['bmx']['series'][0]['datos'])) {
        // Extraer los tipos de cambio de la respuesta de Banxico
        $exchangeRates = $banxicoData['bmx']['series'][0]['datos'];
    } else {
        $exchangeRates = null;
        echo "Error al obtener los tipos de cambio de Banxico.";
    }
}

// API Key de OpenWeatherMap
$apiKey = 'a161dada86bca40aac015a7d3728a8e1'; // Reemplaza con tu propia API key

// Ciudad para la que se quiere obtener el clima
$city = 'Ciudad de México';

// URL para obtener datos del clima
$apiUrl = "http://api.openweathermap.org/data/2.5/weather?q=London,uk&APPID=a161dada86bca40aac015a7d3728a8e1";

// Realizar la solicitud HTTP y decodificar la respuesta JSON
$response = @file_get_contents($apiUrl);
if ($response === FALSE) {
    echo "Error al obtener el pronóstico del tiempo.<br>";
    error_log("Error al obtener el pronóstico del tiempo: No se pudo conectar a la API.");
    $description = "No disponible";
    $temperature = "No disponible";
} else {
    $data = json_decode($response, true);
    if ($data !== null && isset($data['weather'][0]['description']) && isset($data['main']['temp'])) {
        // Extraer la descripción del clima y la temperatura
        $description = $data['weather'][0]['description'];
        $temperature = round($data['main']['temp'], 2); // La temperatura ya está en Celsius debido a 'units=metric'
    } else {
        echo "Error al procesar los datos del pronóstico del tiempo.<br>";
        error_log("Error al procesar los datos del pronóstico del tiempo: " . json_encode($data));
        $description = "No disponible";
        $temperature = "No disponible";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipos de Cambio y Clima</title>
    <style>
        body {
            background-color: #b3e0ff; /* Fondo celeste */
            color: white; /* Letras de color blanco */
            text-align: center; /* Centrar el texto */
        }
        h1 {
            margin-top: 20px;
        }
        #exchange-rates, #weather {
            margin: 20px auto;
            width: 60%;
            padding: 20px;
            border: 1px solid white;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.2); /* Fondo ligeramente transparente */
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h1>Tipos de Cambio Banxico</h1>
    <div id="exchange-rates">
        <ul>
            <?php if ($exchangeRates !== null): ?>
                <?php foreach ($exchangeRates as $rate): ?>
                    <li><?php echo htmlspecialchars($rate['fecha']) . ': ' . htmlspecialchars($rate['dato']); ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No se pudieron obtener los datos de tipos de cambio.</li>
            <?php endif; ?>
        </ul>
    </div>

    <h1>Pronóstico del Tiempo</h1>
    <div id="weather">
        <p>Descripción: <?php echo htmlspecialchars($description); ?></p>
        <p>Temperatura: <?php echo htmlspecialchars($temperature); ?>°C</p>
    </div>
</body>
</html>
