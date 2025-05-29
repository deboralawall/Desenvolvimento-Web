<?php
require_once 'config.php';
session_start();

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    
    if (empty($email) || empty($senha)) {
        $erros[] = "E-mail e senha são obrigatórios";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario'] = $usuario['nome_completo'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['id_usuario'] = $usuario['id'];
                header('Location: main.php');
                exit;
            } else {
                $erros[] = "E-mail ou senha incorretos";
            }
        } catch (PDOException $e) {
            $erros[] = "Erro ao verificar credenciais: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Brechó da Débora</title>
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
            <li><a href="index.php">Início</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="cadastro.php">Cadastre-se</a></li>
            <li><a href="admin.php">Admin</a></li>
        </ul>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2>Login</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($erros)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($erros as $erro): ?>
                                    <p><?php echo $erro; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha:</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Entrar</button>
                        </form>
                        <p class="mt-3">Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Brechó da Débora</p>
    </footer>
</body>
</html>