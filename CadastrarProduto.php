<?php 
require_once 'db_connect.php';
require_once 'validarProduto.php';

if(session_status() === PHP_SESSION_NONE){
  session_start();
}

if (empty($_SESSION['logado']) || ($_SESSION['logado'] !== true) || ($_SESSION['cargo_usuario'] !== 'gerente')){
  session_unset();
  session_destroy();
  header('Location: login.php');
  exit();
}

$nome_usuario = $_SESSION['nome_usuario'];
?>

<?php 
$erros = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST'):

  $validadorProd = new validarProduto();
  $erros = $validadorProd->validar(
    $_POST['nomeProduto'],
    $_POST['estoque'],
    $_POST['precoVenda'],
    $_POST['precoCusto'],
    $_POST['sku'],
    $_POST['codigoBarras'],
    $_POST['unidade'],
    $_POST['categoria']
  );
  
    
    
    if (empty($erros)) {
      try {
        $sql = "INSERT INTO produtos (nome, quant_estoque, preco_unitario, preco_custo, sku, codigo_barras, unidade, tipo) 
                  VALUES (:nome, :quant, :preco_u, :preco_c, :sku, :barras, :unidade, :tipo)";

          $stmt = $pdo->prepare($sql);
          $stmt->bindValue(':nome', $validadorProd->getNome());
          $stmt->bindValue(':quant', $validadorProd->getQuantEstoque());
          $stmt->bindValue(':quant', $validadorProd->getQuantEstoque());
          $stmt->bindValue(':preco_u', $validadorProd->getPrecoUnitario());
          $stmt->bindValue(':preco_c', $validadorProd->getPrecoCusto());
          $stmt->bindValue(':sku', $validadorProd->getSku());
          $stmt->bindValue(':barras', $validadorProd->getCodigoBarras());
          $stmt->bindValue(':unidade', $validadorProd->getUnidade());
          $stmt->bindValue(':tipo', $_POST['categoria']);
         
          
          $stmt->execute();
          header("Location: listarProdutos.php");
      } catch (PDOException $e) {
        $erros[] = "Erro no banco: " . $e->getMessage();
      }
    }
endif;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cadastro de Produto — PDV Instrumentos</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <!-- ===== CABEÇALHO DA PÁGINA ===== -->
  <header class="cabecalho">
    <!-- Ícone decorativo de nota musical -->
    <span class="icone-header">🎸</span>
    <div>
      <h1 class="titulo-header">Cadastro de Produto</h1>
      <!-- Subtítulo informando o contexto do sistema -->
      <p class="subtitulo-header">PDV — Loja de Instrumentos Musicais</p>
    </div>
  </header>

  <!-- ===== CONTAINER CENTRAL DO FORMULÁRIO ===== -->
  <main class="container">

    <!-- Título da seção do formulário -->
    <h2 class="titulo-form">Novo Produto</h2>

    <!-- FORMULÁRIO DE CADASTRO -->
    <!-- onsubmit chama a função JS que valida e processa os dados -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="formProduto" method="post">

      <!-- ----- LINHA 1: Código de Barras + SKU ----- -->
      <div class="linha">

        <!-- Campo: Código de Barras (EAN/UPC) -->
        <div class="campo">
          <label for="codigoBarras">Código de Barras (EAN)</label>
          <input
            type="text"
            id="codigoBarras"
            name="codigoBarras"
            placeholder="Ex: 7891234567890"
            maxlength="14"
          />
        </div>

        <!-- Campo: SKU interno da loja -->
        <div class="campo">
          <label for="sku">SKU Interno <span class="obrigatorio">*</span></label>
          <input
            type="text"
            id="sku"
            name="sku"
            placeholder="Ex: GUIT-001"
            required
          />
        </div>

      </div>

      <!-- ----- LINHA 2: Nome do Produto ----- -->
      <div class="linha">
        <div class="campo campo-largo">
          <label for="nomeProduto">Nome do Produto <span class="obrigatorio">*</span></label>
          <input
            type="text"
            id="nomeProduto"
            name="nomeProduto"
            placeholder="Ex: Guitarra Elétrica Fender Stratocaster"
            required
          />
        </div>
      </div>

      <!-- ----- LINHA 3: Categoria + Marca ----- -->
      <div class="linha">

        <!-- Campo: Categoria do produto (select com opções pré-definidas) -->
        <div class="campo">
          <label for="categoria">Categoria <span class="obrigatorio">*</span></label>
          <select id="categoria" name="categoria" required>
            <option value="" disabled selected>Selecione...</option>
            <option value="cordas">Cordas (Guitarra, Violão, Baixo)</option>
            <option value="teclas">Teclas e Piano</option>
            <option value="sopros">Sopros</option>
            <option value="percussao">Percussão</option>
            <option value="acessorios">Acessórios</option>
            <option value="amplificadores">Amplificadores</option>
            <option value="estudio">Estúdio e Gravação</option>
          </select>
        </div>

        <div class="campo">
          <label for="unidade">Unidade <span class="obrigatorio">*</span></label>
          <select id="unidade" name="unidade" required>
            <option value="" disabled <?php echo !isset($_POST['unidade']) ? 'selected' : ''; ?>>Selecione...</option>
            <option value="UN" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] == 'UN') ? 'selected' : ''; ?>>UN (Unidade)</option>
            <option value="PC" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] == 'PC') ? 'selected' : ''; ?>>PC (Peça)</option>
            <option value="PAR" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] == 'PAR') ? 'selected' : ''; ?>>PR (Par - ex: Baquetas)</option>
            <option value="JG" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] == 'JG') ? 'selected' : ''; ?>>JG (Jogo - ex: Encordoamento)</option>
            <option value="CX" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] == 'CX') ? 'selected' : ''; ?>>CX (Caixa)</option>
            <option value="MT" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] == 'MT') ? 'selected' : ''; ?>>MT (Metro - ex: Cabos de Rolo)</option>
          </select>
        </div>
      </div>

      </div>

      <!-- ----- LINHA 4: Preço de Custo + Preço de Venda + Margem ----- -->
      <div class="linha">

        <!-- Campo: Preço de custo (valor pago ao fornecedor) -->
        <div class="campo">
          <label for="precoCusto">Preço de Custo (R$)</label>
          <!-- oninput chama a função JS que recalcula a margem automaticamente -->
          <input
            type="number"
            id="precoCusto"
            name="precoCusto"
            placeholder="0,00"
            min="0"
            step="0.01"/>
        </div>

        <!-- Campo: Preço de venda ao cliente -->
        <div class="campo">
          <label for="precoVenda">Preço de Venda (R$) <span class="obrigatorio">*</span></label>
          <!-- oninput recalcula a margem sempre que o valor muda -->
          <input
            type="number"
            id="precoVenda"
            name="precoVenda"
            placeholder="0,00"
            min="0"
            step="0.01"
            required/>
        </div>
      </div>

      <!-- ----- LINHA 5: Estoque Atual + Estoque Mínimo + Localização ----- -->
      <div class="linha">

        <!-- Campo: Quantidade atual em estoque -->
        <div class="campo">
          <label for="estoque">Estoque Atual <span class="obrigatorio">*</span></label>
          <input
            type="number"
            id="estoque"
            name="estoque"
            placeholder="0"
            min="0"
            required
          />
        </div>
      </div>

<?php if (!empty($erros)): ?>
    <div style="color:red;">
        <?php foreach($erros as $erro): ?>
            <p><?php echo $erro; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
    
      <!-- ----- ÁREA DE MENSAGEM DE FEEDBACK ----- -->
      <!-- Oculta por padrão; o JS exibe após o envio do formulário -->
      <div id="mensagem" class="mensagem" style="display: none;"></div>

      <!-- ----- BOTÕES DE AÇÃO ----- -->
      <div class="botoes">

        <!-- Botão Limpar: chama a função JS que reseta o formulário -->
        <button type="button" class="btn-limpar" onclick="limparFormulario()">
          Limpar
        </button>

        <!-- Botão Salvar: submete o formulário (dispara onsubmit) -->
        <button type="submit" class="btn-salvar">
          ✓ Salvar Produto
        </button>

      </div>

    </form>

  </main>

  <!-- ----- TABELA DE PRODUTOS CADASTRADOS ----- -->
  <!-- Seção que lista os produtos adicionados durante a sessão -->
  <section class="secao-lista" id="secaoLista" style="display: none;">

  <!-- Importação do arquivo JavaScript externo -->
  <script src="script.js"></script>

</body>
</html>
