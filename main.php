<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Cliente - Brechó da Débora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <header>
    <canvas id="logoCanvas" width="250" height="100"></canvas>
        <script>
            var canvas = document.getElementById("logoCanvas");
            var ctx = canvas.getContext("2d");

            ctx.fillStyle = "#8B4513";
            ctx.beginPath();
            ctx.moveTo(20, 20);
            ctx.lineTo(230, 20);
            ctx.arcTo(250, 20, 250, 40, 20);
            ctx.lineTo(250, 60);
            ctx.arcTo(250, 80, 230, 80, 20);
            ctx.lineTo(20, 80);
            ctx.arcTo(0, 80, 0, 60, 20);
            ctx.lineTo(0, 40);
            ctx.arcTo(0, 20, 20, 20, 20);
            ctx.closePath();
            ctx.fill();

            ctx.font = "bold 18px Arial";
            ctx.fillStyle = "white";
            ctx.textAlign = "center";
            ctx.fillText("Brechó da Débora", 125, 50);
        </script>
    </header>

    <nav>
        <ul>
            <li><a href="main.php">Início</a></li>
            <li><a href="verao.html">Roupa de Verão</a></li>
            <li><a href="inverno.html">Roupa de Inverno</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </nav>

    <div class="container my-5">
        <div class="alert alert-success">
            <h2>Olá, <?php echo $_SESSION['usuario']; ?>!</h2>
            <p>Bem-vindo à sua área exclusiva no Brechó da Débora.</p>
        </div>
        
        <div class="video-container d-flex justify-content-center mt-5">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/QfevfvsCKpg?si=0bjR5DvwTWPE2673" 
                    title="YouTube video player" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Brechó da Débora</p>
    </footer>
</body>
</html>