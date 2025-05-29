<?php
require_once 'config.php';
session_start();

$mensagem = '';
$tipoMensagem = '';

if (isset($_GET['excluir'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM usuario WHERE id = ?");
        $stmt->execute([$_GET['excluir']]);
        
        if ($stmt->rowCount() > 0) {
            $mensagem = "Usuário excluído com sucesso!";
            $tipoMensagem = "success";
        } else {
            $mensagem = "Usuário não encontrado.";
            $tipoMensagem = "warning";
        }
    } catch (PDOException $e) {
        $mensagem = "Erro ao excluir usuário: " . $e->getMessage();
        $tipoMensagem = "danger";
    }
}

try {
    $stmt = $pdo->query("SELECT id, nome_completo, email, data_nasc FROM usuario ORDER BY id DESC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensagem = "Erro ao buscar usuários: " . $e->getMessage();
    $tipoMensagem = "danger";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Brechó da Débora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="estilo.css">
    <style>
        .card-stat {
            transition: transform 0.3s;
        }
        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .user-actions .btn {
            margin: 2px;
        }
        .modal-edit .form-control {
            margin-bottom: 15px;
        }
        #loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div id="loading">
        <div class="spinner"></div>
    </div>

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
        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $tipoMensagem ?> alert-dismissible fade show">
                <?= $mensagem ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <h2 class="mb-4"><i class=""></i> Painel Administrativo</h2>
        
        <div class="card shadow">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class=""></i> Usuários Cadastrados</h4>
                <a href="cadastro.php" class="btn btn-sm btn-success"><i class="bi bi-plus-circle"></i> Novo Usuário</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nome Completo</th>
                                <th>E-mail</th>
                                <th>Data Nasc.</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Nenhum usuário cadastrado</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($usuario['id']) ?></td>
                                        <td><?= htmlspecialchars($usuario['nome_completo']) ?></td>
                                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                                        <td><?= formatarData($usuario['data_nasc']) ?></td>
                                        <td class="user-actions">
                                            <button class="btn btn-sm btn-warning" title="Editar" 
                                                onclick="abrirModalEdicao(
                                                    '<?= $usuario['id'] ?>',
                                                    '<?= htmlspecialchars(addslashes($usuario['nome_completo'])) ?>',
                                                    '<?= htmlspecialchars(addslashes($usuario['email'])) ?>',
                                                    '<?= formatarData($usuario['data_nasc']) ?>'
                                                )">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <a href="admin.php?excluir=<?= $usuario['id'] ?>" class="btn btn-sm btn-danger" 
                                               title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdicao" tabindex="-1" aria-labelledby="modalEdicaoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalEdicaoLabel">Editar Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEdicao" method="POST" action="atualizar_usuario.php">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editId">
                        
                        <div class="mb-3">
                            <label for="editNome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="editNome" name="nome" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editDataNasc" class="form-label">Data de Nascimento</label>
                            <input type="text" class="form-control" id="editDataNasc" name="data_nascimento" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Brechó da Débora - Todos os direitos reservados</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>

    function abrirModalEdicao(id, nome, email, dataNasc) {
        document.getElementById('editId').value = id;
        document.getElementById('editNome').value = nome;
        document.getElementById('editEmail').value = email;
        document.getElementById('editDataNasc').value = dataNasc;
        
        var modal = new bootstrap.Modal(document.getElementById('modalEdicao'));
        modal.show();
    }

    document.getElementById('formEdicao').addEventListener('submit', function(e) {
        e.preventDefault();
        
        document.getElementById('loading').style.display = 'flex';
        
        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na rede: ' + response.status);
            }
            return response.text().then(text => {
                try {
                    return text ? JSON.parse(text) : {};
                } catch (e) {
                    console.error('Erro ao parsear JSON:', text);
                    throw new Error('Resposta inválida do servidor');
                }
            });
        })
        .then(data => {
            if (data.success) {
                alert(data.message || 'Usuário atualizado com sucesso!');
                window.location.reload();
            } else {
                throw new Error(data.message || 'Erro ao atualizar usuário');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro: ' + error.message);
        })
        .finally(() => {
            document.getElementById('loading').style.display = 'none';
        });
    });

    document.querySelectorAll('.btn-danger').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Tem certeza que deseja excluir este usuário?')) {
                e.preventDefault();
            }
        });
    });
</script>
</body>
</html>