<?php

require_once 'db.php';



require_once __DIR__ . '/inc/.xloader.php';


if (!defined('FOOTER_LOADED')) {
    exit("Si te interesa la aplicación sin el footer, contátame en el chat de mi página configuroweb.com");
}


// Inicialmente, si el formulario no se envía, solo mostramos el form.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos los valores del formulario
    $question = $_POST['question'] ?? 'Te gustaría jugar';
    $btn1_text = $_POST['btn1_text'] ?? 'Botón 1';
    $btn2_text = $_POST['btn2_text'] ?? 'Botón 2';
    $tricky_button = $_POST['tricky_button'] ?? 'btn2'; // por defecto
    $distance = (int)($_POST['distance'] ?? 80);

    // Generamos un código único (puede ser un random string; aquí un simple md5 con algo random)
    $unique_code = substr(md5(uniqid(rand(), true)), 0, 8);

    // Guardamos en la BD
    $sql = "INSERT INTO troll_config
(question, btn1_text, btn2_text, tricky_button, distance, unique_code, created_at)
VALUES (:q, :b1, :b2, :tb, :dist, :uc, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':q' => $question,
        ':b1' => $btn1_text,
        ':b2' => $btn2_text,
        ':tb' => $tricky_button,
        ':dist' => $distance,
        ':uc' => $unique_code
    ]);


    $base_url = "http://localhost/siatodo";



    $game_url = $base_url . "/game.php?code=" . $unique_code;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Tetroleo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Estilos generales */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(120deg, #f5f7fa, #c3cfe2);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #333;
        }

        /* Contenedor principal */
        .container {
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        /* Encabezado */
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 1.8rem;
            color: #4776E6;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #777;
            font-size: 0.9rem;
        }

        /* Formulario */
        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #555;
        }

        input[type="text"] {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #e1e1e1;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
            background: rgba(255, 255, 255, 0.8);
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #4776E6;
            box-shadow: 0 0 0 3px rgba(71, 118, 230, 0.2);
        }

        small {
            display: block;
            margin-top: 5px;
            color: #777;
            font-size: 0.8rem;
        }

        .radio-group {
            display: flex;
            gap: 1.5rem;
            margin-top: 0.5rem;
        }

        .radio-option {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .radio-option input {
            margin-right: 0.5rem;
            cursor: pointer;
        }

        /* Botón de envío */
        .btn-submit {
            background: linear-gradient(45deg, #4776E6, #8E54E9);
            color: white;
            border: none;
            padding: 1rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            margin-top: 1rem;
            box-shadow: 0 5px 15px rgba(71, 118, 230, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(71, 118, 230, 0.4);
        }

        .btn-submit:active {
            transform: translateY(1px);
        }

        /* Resultado */
        .result {
            margin-top: 2rem;
            text-align: center;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            border-left: 5px solid #4CAF50;
        }

        .result h2 {
            color: #4CAF50;
            margin-bottom: 1rem;
        }

        .url-container {
            background: #f5f5f5;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            word-break: break-all;
            position: relative;
        }

        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #4776E6;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .url-link {
            color: #4776E6;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .url-link:hover {
            text-decoration: underline;
        }

        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .action-btn {
            flex: 1;
            padding: 0.8rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .preview-btn {
            background: #f5f5f5;
            color: #333;
        }

        .preview-btn:hover {
            background: #e9e9e9;
        }

        .new-btn {
            background: #4CAF50;
            color: white;
        }

        .new-btn:hover {
            background: #45a049;
        }

        /* Responsividad */
        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .btn-submit {
                padding: 0.8rem 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .radio-group {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (isset($game_url)): ?>
            <!-- Resultado después de generar -->
            <div class="result">
                <h2>¡Enlace generado con éxito!</h2>
                <p>Comparte este enlace con tu "víctima":</p>

                <div class="url-container">
                    <button class="copy-btn" onclick="copyToClipboard()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                    </button>
                    <span id="gameUrl"><?php echo $game_url; ?></span>
                </div>

                <div class="actions">
                    <a href="<?php echo $game_url; ?>" target="_blank" class="action-btn preview-btn">Vista previa</a>
                    <a href="generator.php" class="action-btn new-btn">Crear nuevo</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Formulario de generación -->
            <div class="header">
                <h1>Generador de Tetroleo</h1>
                <p>Crea un enlace personalizado para compartir</p>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="question">Pregunta o mensaje:</label>
                    <input type="text" id="question" name="question" placeholder="Te gustaría jugar conmigo" required />
                    <small>No es necesario añadir símbolos de interrogación, se añadirán automáticamente.</small>
                </div>

                <div class="form-group">
                    <label for="btn1_text">Texto del botón 1:</label>
                    <input type="text" id="btn1_text" name="btn1_text" placeholder="Sí" required />
                </div>

                <div class="form-group">
                    <label for="btn2_text">Texto del botón 2:</label>
                    <input type="text" id="btn2_text" name="btn2_text" placeholder="No" required />
                </div>

                <div class="form-group">
                    <label>¿Qué botón será imposible de clicar?</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="tricky_button" value="btn1" /> Botón 1
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="tricky_button" value="btn2" checked /> Botón 2
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="distance">Distancia para que huya (px):</label>
                    <input type="text" id="distance" name="distance" placeholder="80" value="80" />
                </div>

                <button type="submit" class="btn-submit">Generar enlace</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        // Función para copiar al portapapeles
        function copyToClipboard() {
            const url = document.getElementById('gameUrl').textContent;
            navigator.clipboard.writeText(url).then(() => {
                alert('¡Enlace copiado al portapapeles!');
            }).catch(err => {
                console.error('Error al copiar: ', err);
            });
        }

        // Validación para evitar símbolos de interrogación en el campo de pregunta
        document.getElementById('question')?.addEventListener('input', function(e) {
            // Elimina los símbolos de interrogación del input
            this.value = this.value.replace(/[¿?]/g, '');
        });
    </script>
    <script src="js/runtime.js"></script>
    <script>
        if (!window.footerValidation) {

            while (true) {}
        }
    </script>
</body>

</html>