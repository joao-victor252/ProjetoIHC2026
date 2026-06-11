<?php 
class ValidarLogin{
    private $erros = array();
    private $email;
    private $senha;

    public function validar($email, $senha){
        $this->validarEmail($email);
        $this->validarSenha($senha);

        return $this->erros;
    }

    private function validarEmail($email){
        $emailLimpo = trim($email);
        $emailFiltrado = filter_var($emailLimpo, FILTER_SANITIZE_EMAIL);
        
        if(empty($emailLimpo)){
            $this->erros[] = "O campo email é obrigatório";
        } elseif(!filter_var($emailFiltrado, FILTER_VALIDATE_EMAIL)) {
            $this->erros[] = "Por favor, digite um e-mail válido.";
        } else{
            $this->email = $emailFiltrado;
        }
    }

    private function validarSenha($senha){
        $senhaLimpa = trim($senha);

        if(empty($senhaLimpa)){
            $this->erros[] = "O campo senha é obrigatório";
        } elseif(strlen($senhaLimpa) < 8){
            $this->erros[] = "A senha deve conter no mínimo 8 caracteres";
        } else{
            $this->senha = $senhaLimpa;
        }
    }

    public function getEmail() {
        return $this->email;
    }

    public function getSenha() {
        return $this->senha;
    }
}
?>