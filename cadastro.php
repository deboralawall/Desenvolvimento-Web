<?php
require_once 'config.php';
session_start();

$erros = [];
$confirmacao = '';
$dadosFormulario = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {
    $dadosFormulario = [
        'nome' => trim($_POST['nome'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'senha' => trim($_POST['senha'] ?? ''),
        'confirmar_senha' => trim($_POST['confirmar_senha'] ?? ''),
        'data_nascimento' => trim($_POST['data_nascimento'] ?? ''),
        'termos' => isset($_POST['termos']) ? true : false
    ];

    if (empty($dadosFormulario['nome'])) {
        $erros[] = "Nome é obrigatório";
    }

    if (empty($dadosFormulario['email'])) {
        $erros[] = "E-mail é obrigatório";
    } elseif (!filter_var($dadosFormulario['email'], FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuario WHERE email = ?");
        $stmt->execute([$dadosFormulario['email']]);
        if ($stmt->fetch()) {
            $erros[] = "Este e-mail já está cadastrado";
        }
    }

    if (empty($dadosFormulario['senha'])) {
        $erros[] = "Senha é obrigatória";
    } elseif (strlen($dadosFormulario['senha']) < 6) {
        $erros[] = "Senha deve ter pelo menos 6 caracteres";
    }

    if ($dadosFormulario['senha'] !== $dadosFormulario['confirmar_senha']) {
        $erros[] = "Senhas não coincidem";
    }

    if (empty($dadosFormulario['data_nascimento'])) {
        $erros[] = "Data de nascimento é obrigatória";
    } else {
        $partes = explode('/', $dadosFormulario['data_nascimento']);
        if (count($partes) !== 3 || !checkdate($partes[1], $partes[0], $partes[2])) {
            $erros[] = "Data inválida (use dd/mm/aaaa)";
        }
    }

    if (!$dadosFormulario['termos']) {
        $erros[] = "Você deve aceitar os termos";
    }

    if (empty($erros)) {
        try {
            $senha_cifrada = password_hash($dadosFormulario['senha'], PASSWORD_DEFAULT);
            $data_nascimento = DateTime::createFromFormat('d/m/Y', $dadosFormulario['data_nascimento']);
            $data_mysql = $data_nascimento->format('Y-m-d');

            $stmt = $pdo->prepare("INSERT INTO usuario (nome_completo, email, data_nasc, senha) 
                                  VALUES (:nome, :email, :data_nasc, :senha)");
            
            $stmt->execute([
                ':nome' => $dadosFormulario['nome'],
                ':email' => $dadosFormulario['email'],
                ':data_nasc' => $data_mysql,
                ':senha' => $senha_cifrada
            ]);

            $confirmacao = "<div class='alert alert-success'><h3>Cadastro realizado com sucesso!</h3>";
            $confirmacao .= "<p><strong>Nome:</strong> " . htmlspecialchars($dadosFormulario['nome']) . "</p>";
            $confirmacao .= "<p><strong>E-mail:</strong> " . htmlspecialchars($dadosFormulario['email']) . "</p>";
            $confirmacao .= "<p><strong>Data de Nascimento:</strong> " . htmlspecialchars($dadosFormulario['data_nascimento']) . "</p>";
            $confirmacao .= "<p>Você já pode <a href='login.php'>fazer login</a>.</p></div>";
            
            $dadosFormulario = [];
            
        } catch (PDOException $e) {
            $erros[] = "Erro ao cadastrar usuário: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Brechó da Débora</title>
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
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h2>Cadastre-se</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($erros)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($erros as $erro): ?>
                                <p><?php echo $erro; ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($confirmacao)): ?>
                        <?php echo $confirmacao; ?>
                    <?php else: ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo:</label>
                                <input type="text" class="form-control" id="nome" name="nome" 
                                    value="<?php echo htmlspecialchars($dadosFormulario['nome'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail:</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                    value="<?php echo htmlspecialchars($dadosFormulario['email'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="data_nascimento" class="form-label">Data de Nascimento:</label>
                                <input type="text" class="form-control" id="data_nascimento" name="data_nascimento" 
                                    value="<?php echo htmlspecialchars($dadosFormulario['data_nascimento'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha:</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                            </div>

                            <div class="mb-3">
                                <label for="confirmar_senha" class="form-label">Confirmar Senha:</label>
                                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="termos" name="termos" required>
                                <label class="form-check-label" for="termos">Aceito os termos e condições</label>
                            </div>
                            
                            <button type="submit" name="cadastrar" class="btn btn-success">Cadastrar</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Brechó da Débora - Todos os direitos reservados</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>