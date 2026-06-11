<?php 
//Conexão
require_once 'db_connect.php';
require_once 'validacao.php';

//Ativar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$erros = array();
//Botão logar
if(isset($_POST['log-btn'])):
  
    $validador = new ValidarLogin();

    $erros = $validador->validar($_POST['email'], $_POST['senha']);

    if(empty($erros)):

        $email_validado = $validador->getEmail();
        $senha_validada = $validador->getSenha();

        try {
            $sql = "SELECT id, nome, email, senha FROM funcionarios WHERE email = :email LIMIT 1";

            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':email', $email_validado);

            $stmt->execute();

            $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

            if($funcionario):
                if($senha_validada == $funcionario['senha']):
                    $_SESSION['logado'] = true;
                    $_SESSION['id_usuario'] = $funcionario['id'];
                    $_SESSION['nome_usuario'] = $funcionario['nome'];

                    header('Location: pdv-main.php');
                    exit();
                else:
                    $erros[] = "Senha incorreta.";
                endif;
            else:
                $erros[] = "E-mail não cadastrado.";
            endif;
            
        } catch(PDOException $e){
            $erros[] = "Erro no sistema" . $e->getMessage();
        }
    endif;
endif;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilização/Login.css">
    <title>PDV SYSTEM| Login</title>
</head>
<body>
    <div class="cadastro-container">
        <h1 class="login">Login</h1>

        <form action="<?=$_SERVER['PHP_SELF']; ?>" method="POST">
        
        <label for="nome">E-mail: <span>*</span></label>
        <input type="email" name="email" id="email" placeholder="ex@gmail.com:" >

        <label for="senha">Senha: <span>*</span></label>
        <input type="password" name="senha" id="senha"  minlength="8" placeholder="Digite sua senha:">

        <?php if(!empty($erros)): ?>
            <div class="bloco-erros" style="color: red; margin-bottom: 15px;">
            <?php foreach($erros as $erro): ?>
                <p><?= $erro; ?></p>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
            
        <button type="submit" name="log-btn" class="log-btn">Entrar</button>

        </form>
         
        <footer>
            Problemas ao acessar? Contate o gerente.
        </footer>
    </div>
</body>
</html>