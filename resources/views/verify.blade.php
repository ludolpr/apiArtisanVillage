<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de votre email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .container {
            background-color: #ffffff;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #d9b99b;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
        }

        .button {
            display: inline-block;
            font-size: 16px;
            color: #ffffff;
            background-color: #d9b99b;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin: 20px 0;
        }

        .button:hover {
            background-color: #9a7d6b;
        }

        .footer {
            font-size: 14px;
            color: #888;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Bienvenue parmi nous !</h1>
        <p>Pour finaliser votre inscription, veuillez vérifier votre adresse email en cliquant sur le bouton ci-dessous
            :</p>
        <a href="{{ $verificationUrl }}" class="button">Vérifiez votre email</a>
        <p class="footer">Si vous n'avez pas demandé cette inscription, vous pouvez ignorer cet email.</p>
    </div>
</body>

</html>