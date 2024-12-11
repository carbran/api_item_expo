<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificação</title>
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #header {
            background-color: #99a3f1;
            color: #fff;
            padding: 20px;
        }

        #header h1 {
            margin:0;
            text-align: center;
        }

        #logo {
            max-width: 30%;
            height: auto;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        #content {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div id="header">
        <img id="logo" src="https://i.imgur.com/fI1bCUj.png" alt="Logo">
    </div>

    <div id="content">
        <p><span class="highlight">O seu código de verificação é:</span></p>

        <strong style="font-size: 24px; font-weight: bold;">{{ $temporaryAccessCode->access_code }}</strong>

        <br>

        <p>Por favor, retorne à página de login e insira o <br> código acima para confirmar sua identidade.</p>

        <p>Obrigado,<br>
        {{ config('app.name') }}</p>
    </div>

</body>
</html>
