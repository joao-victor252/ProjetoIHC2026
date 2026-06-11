<?php 
// Inicia a sessão atual para que o PHP saiba de quem estamos falando
session_start();

// Limpa todas as variáveis salvas na sessão do usuário (como o id e o nome)
session_unset();

// Destrói completamente a sessão registrada no servidor
session_destroy();

// Redireciona o usuário de volta para a página de login
header("Location: login.php");
exit(); // Garante que o script pare de ser executado aqui
?>