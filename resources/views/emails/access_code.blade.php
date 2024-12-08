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
            margin: 0;
            text-align: left;
        }

        #logo {
            max-width: 100%;
            height: auto;
        }

        #content {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            text-align: left;
        }
    </style>
</head>
<body>

    <div id="header">
        <img id="logo" src="{{ asset('images/item_expo_logo.png') }}" alt="Logo">
    </div>

    <div id="content">
        <p><span class="highlight">O seu código de verificação é:</span></p>

        <strong style="font-size: 24px; font-weight: bold;">{{ $codigoAcessoTemporario->codigo_acesso }}</strong>

        <br>
        <br>

        <p>Por favor, retorne à página de login e insira o </br> código acima para confirmar sua identidade.</p>

        <p>Obrigado,<br>
        {{ config('app.name') }}</p>
    </div>

</body>
</html>
