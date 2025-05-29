<?php
require_once 'config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit;
}

$response = ['success' => false];

try {
    $dados = [
        'id' => $_POST['id'],
        'nome' => trim($_POST['nome'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'data_nascimento' => trim($_POST['data_nascimento'] ?? '')
    ];

    if (empty($dados['nome']) || empty($dados['email']) || empty($dados['data_nascimento'])) {
        $response['message'] = 'Todos os campos são obrigatórios';
        echo json_encode($response);
        exit;
    }

    $partes = explode('/', $dados['data_nascimento']);
    if (count($partes) !== 3 || !checkdate($partes[1], $partes[0], $partes[2])) {
        $response['message'] = 'Data inválida (use dd/mm/aaaa)';
        echo json_encode($response);
        exit;
    }

    $data_nascimento = DateTime::createFromFormat('d/m/Y', $dados['data_nascimento']);
    $data_mysql = $data_nascimento->format('Y-m-d');

    $stmt = $pdo->prepare("UPDATE usuario SET nome_completo = :nome, email = :email, data_nasc = :data_nasc WHERE id = :id");
    $stmt->execute([
        ':id' => $dados['id'],
        ':nome' => $dados['nome'],
        ':email' => $dados['email'],
        ':data_nasc' => $data_mysql
    ]);

    $response['success'] = true;
    $response['message'] = 'Usuário atualizado com sucesso';
} catch (PDOException $e) {
    $response['message'] = 'Erro ao atualizar usuário: ' . $e->getMessage();
}

echo json_encode($response);