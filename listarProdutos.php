<?php 

require_once 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Controle de acesso (Mantendo o padrão que você usa para o Gerente)
if (empty($_SESSION['logado']) || ($_SESSION['logado'] !== true) || ($_SESSION['cargo_usuario'] !== 'gerente')) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

$nome_usuario = $_SESSION['nome_usuario'];

// Busca os produtos no banco de dados usando o $pdo do seu db_connect.php
try {
    // Seleciona exatamente as colunas solicitadas para produtos
    $sql = "SELECT nome, quant_estoque, preco_unitario, preco_custo, sku, codigo_barras, unidade, tipo FROM produtos ORDER BY nome ASC";
    $stmt = $pdo->query($sql);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Caso aconteça algum erro na consulta
    $erro_banco = "Erro ao buscar produtos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV SYSTEM | Lista de Produtos</title>
    <style>
        /* 1. RESET E FUNDO VERDE DA APLICAÇÃO (Idêntico à sua referência) */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #2e8b57; 
            color: #333;
            padding: 20px;
            min-height: 100vh; 
        }

        /* 2. CONTAINER BRANCO */
        .listagem-container {
            max-width: 1200px; /* Um pouco mais largo para acomodar bem as colunas de produtos */
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 18px; 
            box-shadow: 10px 10px 10px rgb(31, 93, 31); 
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
            color: rgb(229, 50, 50); 
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

        /* Alinhamento numérico para colunas financeiras/estoque */
        .text-right {
            text-align: right;
        }

        /* Pequenas tags estilizadas para Unidade e Tipo (Opcional, deixa o layout moderno) */
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 600;
            background-color: #e2e8f0;
            color: #4a5568;
        }

        /* 5. BOTÃO VOLTAR / CADAS TRAR (Padrão do seu cad-btn) */
        .btn-voltar {
            display: inline-block;
            margin-top: 25px;
            padding: 8px 15px;
            background-color: #3ebf76; 
            color: white;
            font-weight: bolder;
            text-decoration: none;
            border-radius: 15px;
            box-shadow: 6px 6px 10px rgb(31, 93, 31);
            transition: transform 0.5s;
        }

        .btn-voltar:hover {
            transform: scale(1.15); 
        }
    </style>
</head>
<body>
    <div class="listagem-container">
        <div class="usuario-logado">
            Gerente: <strong><?= htmlspecialchars($nome_usuario) ?></strong> | <a href="logout.php">Sair</a>
        </div>

        <h1>Produtos Cadastrados</h1>

        <?php if (isset($erro_banco)): ?>
            <p style="color: red;"><?= $erro_banco ?></p>
        <?php elseif (empty($produtos)): ?>
            <p style="margin-top: 20px; color: #666;">Nenhum produto cadastrado no sistema.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Cód. Barras</th>
                        <th>Nome do Produto</th>
                        <th>Unidade</th>
                        <th>Tipo</th>
                        <th class="text-right">Qtd. Estoque</th>
                        <th class="text-right">Preço Custo</th>
                        <th class="text-right">Preço Venda</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $prod): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($prod['sku']) ?></strong></td>
                            <td><?= htmlspecialchars($prod['codigo_barras']) ?></td>
                            <td><?= htmlspecialchars($prod['nome']) ?></td>
                            <td><span class="badge"><?= htmlspecialchars($prod['unidade']) ?></span></td>
                            <td><?= htmlspecialchars($prod['tipo']) ?></td>
                            
                            <td class="text-right"><?= number_format($prod['quant_estoque'], 0, ',', '.') ?></td>
                            
                            <td class="text-right">R$ <?= number_format($prod['preco_custo'], 2, ',', '.') ?></td>
                            <td class="text-right">R$ <?= number_format($prod['preco_unitario'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="cadastrarProduto.php" class="btn-voltar">Cadastrar Novo Produto</a>
    </div>
</body>
</html>