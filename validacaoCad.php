<?php 
class validarCad {
    // Propriedades privadas para encapsular os dados limpos
    private $nome;
    private $data_nasc;
    private $cpf;
    private $tel1;
    private $tel2;
    private $email;
    private $cargo;
    private $salario;
    private $senha;
    private $erros = array();

    // Método principal (Orquestrador público)
    public function validar($nome, $data_nasc, $cpf, $tel1, $tel2, $email, $cargo, $salario, $senha) {
        $this->validarNome($nome);
        $this->validarDataNasc($data_nasc);
        $this->validarCpf($cpf);
        $this->validarTel1($tel1);
        $this->validarTel2($tel2);
        $this->validarEmail($email);
        $this->validarCargo($cargo);
        $this->validarSalario($salario);
        $this->validarSenha($senha);

        return $this->erros;
    }

    // ==========================================
    // MÉTODOS PRIVADOS DE VALIDAÇÃO / SANITIZAÇÃO
    // ==========================================

    private function validarNome($nome) {
        $nome = trim($nome);
        $nome = filter_var($nome, FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($nome) || mb_strlen($nome) < 3) {           
            $this->erros[] = "O nome precisa ter no mínimo 3 caracteres.";
            return;
        }

        if (!preg_match("/^[\p{L}\s'-]+$/u", $nome)) {
            $this->erros[] = "O nome contém caracteres inválidos.";
        } else {
            $this->nome = $nome;
        }
    }

    private function validarDataNasc($data_nasc) {
        $d = DateTime::createFromFormat('Y-m-d', $data_nasc);
    
        if ($d && $d->format('Y-m-d') === $data_nasc) {
            $this->data_nasc = $data_nasc;
        } else {
            $this->erros[] = "A data de nascimento inserida é inválida.";
        }
    }

    private function validarCpf($cpf) {
        // Remove qualquer caractere que não seja número
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11) {
            $this->erros[] = "O CPF deve conter exatamente 11 dígitos numéricos.";
            return;
        }

        // Evita CPFs com todos os números iguais (ex: 111.111.111-11)
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            $this->erros[] = "CPF inválido.";
            return;
        }

        // Validação matemática dos dígitos verificadores do CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                $this->erros[] = "CPF inválido.";
                return;
            }
        }

        $this->cpf = $cpf;
    }

    private function validarTel1($tel1) {
        $tel1 = preg_replace('/\D/', '', $tel1);

        if (strlen($tel1) < 10 || strlen($tel1) > 11) {
            $this->erros[] = "O Telefone 1 deve conter 10 (fixo) ou 11 (celular) dígitos com DDD.";
        } else {
            $this->tel1 = $tel1;
        }
    }

    private function validarTel2($tel2) {
        // Como o Telefone 2 não é obrigatório no HTML:
        if (empty(trim($tel2))) {
            $this->tel2 = null;
            return;
        }

        $tel2 = preg_replace('/\D/', '', $tel2);

        if (strlen($tel2) < 10 || strlen($tel2) > 11) {
            $this->erros[] = "O Telefone 2 inserido é inválido.";
        } else {
            $this->tel2 = $tel2;
        }
    }

    private function validarEmail($email) {
        $emailLimpo = filter_var(trim($email), FILTER_VALIDATE_EMAIL);

        if (!$emailLimpo) {
            $this->erros[] = "O e-mail inserido não é válido.";
        } else {
            $this->email = $emailLimpo;
        }
    }

    private function validarCargo($cargo) {
        $cargos_permitidos = ['vendedor', 'gerente', 'estoquista'];

        if (!in_array($cargo, $cargos_permitidos)) {
            $this->erros[] = "O cargo selecionado é inválido.";
        } else {
            $this->cargo = $cargo;
        }
    }

    private function validarSalario($salario) {
        $salarioFloat = filter_var($salario, FILTER_VALIDATE_FLOAT);

        if ($salarioFloat === false || $salarioFloat < 0) {
            $this->erros[] = "O salário deve ser um valor numérico positivo.";
        } else {
            $this->salario = $salarioFloat;
        }
    }

    private function validarSenha($senha) {
        if (strlen($senha) < 8) {
            $this->erros[] = "A senha precisa ter no mínimo 8 caracteres.";
        } else {
            // CRIPTOGRAFIA: Já guarda a senha hashada e segura para o banco
            $this->senha = password_hash($senha, PASSWORD_DEFAULT);
        }
    }

    // ==========================================
    // METODOS GETTERS (Para puxar os dados limpos)
    // ==========================================

    public function getNome() { return $this->nome; }
    public function getDataNasc() { return $this->data_nasc; }
    public function getCpf() { return $this->cpf; }
    public function getTel1() { return $this->tel1; }
    public function getTel2() { return $this->tel2; }
    public function getEmail() { return $this->email; }
    public function getCargo() { return $this->cargo; }
    public function getSalario() { return $this->salario; }
    public function getSenha() { return $this->senha; }
}
?>