<?php 

require_once 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['logado']) || ($_SESSION['logado'] !== true) || ($_SESSION['cargo_usuario'] !== 'gerente')) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

$nome_usuario = $_SESSION['nome_usuario'];

// 3. Busca os funcionários no banco de dados usando o $pdo do seu db_connect.php
try {
    // Selecionamos os campos baseados no seu INSERT (exceto a senha por segurança)
    $sql = "SELECT id, nome, data_nasc, cpf, telefone_1, telefone_2, email, cargo, salario, situacao FROM funcionarios ORDER BY nome ASC";
    $stmt = $pdo->query($sql);
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Caso aconteça algum erro na consulta
    $erro_banco = "Erro ao buscar funcionários: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV SYSTEM | Lista de Funcionários</title>
    <style>
        /* 1. RESET E FUNDO VERDE DA APLICAÇÃO */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #2e8b57; /* O mesmo verde do seu cadastro */
            color: #333;
            padding: 20px;
            min-height: 100vh; /* Garante que o fundo verde cubra a tela toda */
        }

        /* 2. CONTAINER BRANCO (Igual ao estilo do seu cadastro) */
        .listagem-container {
            max-width: 1100px;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 18px; /* Bordas arredondadas idênticas ao cadastro */
            box-shadow: 10px 10px 10px rgb(31, 93, 31); /* A mesma sombra marcante */
        }

        /* 3. TOPO DO PAINEL (Usuário Logado) */
        .usuario-logado {
            text-align: right;
            margin-bottom: 20px;
            font-size: 0.9em;
            color: #666;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .usuario-logado strong {
            color: #2e8b57;
        }

        .usuario-logado a {
            color: rgb(229, 50, 50); /* O mesmo vermelho do span do cadastro */
            text-decoration: none;
            font-weight: bold;
            margin-left: 5px;
        }

        .usuario-logado a:hover {
            text-decoration: underline;
        }

        /* 4. TÍTULOS E TABELA */
        h1 {
            font-size: 25px;
            padding-bottom: 10px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            color: #333;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        /* 5. BOTÃO VOLTAR (Padrão do seu cad-btn) */
        .btn-voltar {
            display: inline-block;
            margin-top: 25px;
            padding: 8px 15px;
            background-color: #3ebf76; /* Verde brilhante do seu botão */
            color: white;
            font-weight: bolder;
            text-decoration: none;
            border-radius: 15px;
            box-shadow: 6px 6px 10px rgb(31, 93, 31);
            transition: transform 0.5s;
        }

        .btn-voltar:hover {
            transform: scale(1.15); /* Efeito de crescimento igual ao cadastro */
        }
    </style>
</head>
<body>
    <div class="listagem-container">
        <div class="usuario-logado">
            Gerente: <strong><?= htmlspecialchars($nome_usuario) ?></strong> | <a href="logout.php">Sair</a>
        </div>

        <h1>Funcionários Cadastrados</h1>

        <?php if (isset($erro_banco)): ?>
            <p style="color: red;"><?= $erro_banco ?></p>
        <?php elseif (empty($funcionarios)): ?>
            <p>Nenhum funcionário cadastrado no sistema.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Telefone</th>
                        <th>Cargo</th>
                        <th>Salário</th>
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($funcionarios as $func): ?>
                        <tr>
                            <td><?= htmlspecialchars($func['nome']) ?></td>
                            <td><?= htmlspecialchars($func['cpf']) ?></td>
                            <td><?= htmlspecialchars($func['email']) ?></td>
                            <td><?= htmlspecialchars($func['telefone_1']) ?></td>
                            <td><?= htmlspecialchars($func['telefone_2']) ?></td>
                            <td><?= ucfirst(htmlspecialchars($func['cargo'])) ?></td>
                            <td>R$ <?= number_format($func['salario'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($func['situacao']) ?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="cadastro.php" class="btn-voltar">Cadastrar Novo Funcionário</a>
    </div>
</body>
</html>