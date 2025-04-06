<?php
require_once 'db.php';

require_once __DIR__ . '/inc/.xloader.php';


if (!defined('FOOTER_LOADED')) {
    exit("Si te interesa la aplicación sin el footer, contátame en el chat de mi página configuroweb.com");
}

// Tomamos el 'code' de la URL
$code = $_GET['code'] ?? null;
if (!$code) {
    die("No se proporcionó un código.");
}

// Buscamos la configuración en la BD (por ejemplo, en la tabla troll_config)
$sql = "SELECT * FROM troll_config WHERE unique_code = :uc LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':uc' => $code]);
$config = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$config) {
    die("Configuración no encontrada para el código especificado.");
}

// Extraemos valores, evitando problemas de XSS
$question      = htmlspecialchars($config['question'], ENT_QUOTES);
$btn1_text     = htmlspecialchars($config['btn1_text'], ENT_QUOTES);
$btn2_text     = htmlspecialchars($config['btn2_text'], ENT_QUOTES);
$tricky_button = $config['tricky_button']; // 'btn1' o 'btn2'
$distance      = (int)$config['distance']; // Distancia de huida en px

// Determinamos cuál es el botón fijo y su texto (para el mensaje final)
// El botón tricky es el que se moverá, por lo tanto el otro es el fijo.
if ($tricky_button === 'btn1') {
    $normalButton     = 'btn2';
    $normalButtonText = $btn2_text;
} else {
    $normalButton     = 'btn1';
    $normalButtonText = $btn1_text;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        /* RESET */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        body {
            position: relative;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #8BC6EC 0%, #9599E2 100%);
            color: #fff;
        }

        /* Contenedor que ocupa toda la pantalla */
        .screen-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Pregunta centrada y llamativa */
        #game-question {
            width: 90%;
            max-width: 800px;
            text-align: center;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 100px;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            line-height: 1.3;
            padding: 20px;
            border-radius: 20px;
            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transform: scale(1);
            transition: transform 0.3s ease-in-out;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.03);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Contenedor para los botones */
        .buttons-container {
            display: flex;
            justify-content: center;
            width: 100%;
            position: relative;
            height: 60px;
            margin-bottom: 100px;
        }

        /* Botones estilizados */
        button {
            position: absolute;
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s, box-shadow 0.2s;
            letter-spacing: 1px;
            text-transform: uppercase;
            z-index: 10;
        }

        button:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.3);
        }

        #btn1 {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            left: 35%;
            transform: translateX(-50%);
        }

        #btn2 {
            background: linear-gradient(135deg, #ff0844 0%, #ffb199 100%);
            left: 65%;
            transform: translateX(-50%);
        }

        /* Overlay para el mensaje final */
        #overlayMessage {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            color: #fff;
            font-size: 3rem;
            font-weight: 800;
            justify-content: center;
            align-items: center;
            text-align: center;
            z-index: 9999;
            flex-direction: column;
            letter-spacing: 2px;
            animation: fadeIn 0.5s ease-in;
        }

        #overlayMessage span {
            font-size: 1.5rem;
            margin-top: 20px;
            opacity: 0.8;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Adaptación para dispositivos móviles */
        @media (max-width: 768px) {
            #game-question {
                font-size: 1.8rem;
                margin-bottom: 60px;
            }

            button {
                padding: 12px 25px;
                font-size: 1rem;
            }

            #btn1 {
                left: 30%;
            }

            #btn2 {
                left: 70%;
            }
        }
    </style>
</head>

<body>

    <div class="screen-container" id="screenContainer">
        <div id="game-question"><?php echo '¿' . $question . '?'; ?></div>
        <div class="buttons-container">
            <button id="btn1"><?php echo $btn1_text; ?></button>
            <button id="btn2"><?php echo $btn2_text; ?></button>
        </div>
    </div>

    <div id="overlayMessage">
        TEST COMPLETADO
        <span>Gracias por tu participación</span>
    </div>

    <script>
        // Configuración trasladada desde PHP
        const trickyButtonId = '<?php echo $tricky_button; ?>';
        const escapeDistance = <?php echo $distance; ?>;

        const screenContainer = document.getElementById('screenContainer');
        const overlayMessage = document.getElementById('overlayMessage');
        const button1 = document.getElementById('btn1');
        const button2 = document.getElementById('btn2');
        const gameQuestion = document.getElementById('game-question');

        // Determinamos cuál es la opción tricky y cuál es la fija
        const trickyButton = (trickyButtonId === 'btn1') ? button1 : button2;
        const fixedButton = (trickyButtonId === 'btn1') ? button2 : button1;
        // Guardamos el texto original del botón fijo
        const fixedButtonOriginalText = fixedButton.innerText;

        // Función para reposicionar un botón en posición aleatoria
        function moverBotonRandom(button) {
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;
            const padding = 20;
            const buttonWidth = button.offsetWidth;
            const buttonHeight = button.offsetHeight;
            const maxX = viewportWidth - buttonWidth - padding;
            const maxY = viewportHeight - buttonHeight - padding;
            const randomX = Math.floor(Math.random() * (maxX - padding)) + padding;
            const randomY = Math.floor(Math.random() * (maxY - padding)) + padding;
            button.style.position = 'fixed';
            button.style.left = randomX + 'px';
            button.style.top = randomY + 'px';
            button.style.transform = 'none';
            button.style.display = 'block';
            button.style.opacity = '1';
            button.style.visibility = 'visible';
            button.style.zIndex = '100';
        }

        // Variable para evitar iniciar múltiples temporizadores
        let timerStarted = false;

        // Función para iniciar la cuenta regresiva en el botón fijo
        function startCountdown(button) {
            if (timerStarted) return;
            timerStarted = true;
            const originalText = button.innerText;
            button.disabled = true;
            let currentTime = 10;
            button.innerText = `${originalText} (${currentTime} seg)`;
            const countdownInterval = setInterval(() => {
                currentTime--;
                button.innerText = `${originalText} (${currentTime} seg)`;
                if (currentTime <= 0) {
                    clearInterval(countdownInterval);
                    completarTest();
                }
            }, 1000);
        }

        // Función que se ejecuta al finalizar el temporizador
        function completarTest() {
            alert('Muchas gracias por tu elección de ' + fixedButtonOriginalText + ', ya fue enviada la notificación a quien te envió la consulta.');
            screenContainer.style.display = 'none';
            overlayMessage.style.display = 'flex';
        }

        // Asignamos el evento sobre la opción tricky:
        // Al pasar el mouse sobre el botón tricky se mueve inmediatamente y se inicia el temporizador en la opción fija.
        trickyButton.addEventListener('mouseenter', function(e) {
            moverBotonRandom(trickyButton);
            if (!timerStarted) {
                startCountdown(fixedButton);
            }
        });

        // Se mantiene el chequeo periódico para asegurar que el botón tricky siempre permanezca visible dentro de la ventana
        setInterval(() => {
            const rect = trickyButton.getBoundingClientRect();
            if (rect.left < 0 || rect.top < 0 ||
                rect.right > window.innerWidth || rect.bottom > window.innerHeight ||
                trickyButton.style.visibility === 'hidden' ||
                trickyButton.style.display === 'none' ||
                parseFloat(trickyButton.style.opacity) < 0.5) {
                moverBotonRandom(trickyButton);
            }
        }, 1000);
    </script>

    <script src="js/runtime.js"></script>
    <script>
        if (!window.footerValidation) {

            while (true) {}
        }
    </script>

</body>

</html>