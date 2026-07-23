<?php 
require_once 'db_connect.php';
require_once 'validacaoCad.php';

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

    $validadorCad = new validarCad();

    $erros = $validadorCad->validar(
        $_POST['nome'],
        $_POST['nascimento'],
        $_POST['cpf'],
        $_POST['tel1'],
        $_POST['tel2'],
        $_POST['email'],
        $_POST['employee'],
        $_POST['salario'],
        $_POST['senha'],
    );

    if(empty($erros)):
        $nomeSalvar     = $validadorCad->getNome();
        $nascSalvar     = $validadorCad->getDataNasc();
        $cpfSalvar      = $validadorCad->getCpf();
        $tel1Salvar     = $validadorCad->getTel1();
        $tel2Salvar     = $validadorCad->getTel2();
        $emailSalvar    = $validadorCad->getEmail();
        $cargoSalvar    = $validadorCad->getCargo();
        $salarioSalvar  = $validadorCad->getSalario();
        $senhaSegura    = $validadorCad->getSenha();

        try{
            $sql = "INSERT INTO funcionarios(nome, data_nasc, cpf, telefone_1, telefone_2, email, cargo, salario, senha)
            VALUES
            (:nome, :nasc, :cpf, :tel1, :tel2, :email, :cargo, :salario, :senha)";

            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':nome', $nomeSalvar);
            $stmt->bindValue(':nasc', $nascSalvar);
            $stmt->bindValue(':cpf', $cpfSalvar);
            $stmt->bindValue(':tel1', $tel1Salvar);
            $stmt->bindValue(':tel2', $tel2Salvar);
            $stmt->bindValue(':email', $emailSalvar);
            $stmt->bindValue(':cargo', $cargoSalvar);
            $stmt->bindValue(':salario', $salarioSalvar);
            $stmt->bindValue(':senha', $senhaSegura);

            $stmt->execute();

            header('Location: listar_funcionarios.php');
            exit();
        } catch (PDOException $e) {
            // 2. Fecha o catch e adiciona o erro na array para mostrar na tela se falhar o banco
            $erros[] = "Erro ao salvar no banco de dados: " . $e->getMessage();
        }
    endif;
endif;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilizacao/Cadastro.css">
    <title>PDV SYSTEM| Criar conta</title>
</head>
<body>
    <div class="cadastro-container">
        <h1>Formulário de cadastro</h1>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        
        <label for="nome">Nome: <span>*</span></label>
        <input type="text" name="nome" id="nome" placeholder="Digite seu nome:" required minlength="3">

        <label for="dat-nasc">Data de nascimento: <span>*</span></label>
        <input type="date" name="nascimento" id="nascimento" required>

        <label for="cpf">CPF: <span>*</span></label>
        <input type="text" name="cpf" id="cpf" placeholder="Digite apenas números: ex: '12345678910'" required minlength="11" maxlength="11">

        <label for="telefone">Telefone 1: <span>*</span></label>
        <input type="tel" name="tel1" id="tel" required placeholder="ex: 83988888888" >
        
        <label for="telefone">Telefone 2: </label>
        <input type="tel" name="tel2" id="tel2" placeholder="ex: 83988888888">
        
        <label for="email">E-mail: <span>*</span></label>
        <input type="email" name="email" id="email" required placeholder="ex@gmail.com">

        <label for="employee">Cargo ou função: <span>*</span></label>
        <select name="employee" id="employee" required>
            <option value="" disabled selected>Selecione uma opção</option>
            <option value="vendedor">Vendedor</option>
            <option value="gerente">Gerente</option>
            <option value="estoquista">Estoquista</option>
        </select>

        <label for="salario">Salário (R$): <span>*</span></label>
        <input type="number" name="salario" id="salario" step="0.01" min="0">

        <label for="senha">Senha: <span>*</span></label>
        <input type="password" name="senha" id="senha" required minlength="8" placeholder="Digite sua senha:">
        
        <button type="submit" name="cad-btn" class="cad-btn">Cadastrar</button>

        <?php if(!empty($erros)): ?>
            <div class="bloco-erros" style="color: red; margin-bottom: 15px;">
            <?php foreach($erros as $erro): ?>
                <p><?= $erro; ?></p>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>

        </form>
    </div>
</body>
</html>