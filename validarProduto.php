<?php
class validarProduto {
    private $nome;
    private $quantEstoque;
    private $precoUnitario;
    private $precoCusto;
    private $sku;
    private $codigoBarras;
    private $unidade;
    private $erros = array();

    public function validar($nome, $quantEstoque, $precoUnitario, $precoCusto, $sku, $codigoBarras, $unidade) {
        
        // 1. Validação do Nome
        $this->nome = strip_tags(trim($nome));
        if (empty($this->nome) || strlen($this->nome) < 2) {
            $this->erros[] = "O nome do produto é obrigatório e deve ter pelo menos 2 caracteres.";
        }

        // 2. Validação da Quantidade em Estoque (DECIMAL 10,3)
        $quantEstoque = str_replace(',', '.', $quantEstoque); // Troca vírgula por ponto
        $this->quantEstoque = filter_var($quantEstoque, FILTER_VALIDATE_FLOAT);
        if ($this->quantEstoque === false || $this->quantEstoque < 0) {
            $this->erros[] = "A quantidade em estoque deve ser um número maior ou igual a zero.";
        }

        // 3. Validação do Preço Unitário (Venda) (DECIMAL 10,2)
        $precoUnitario = str_replace(',', '.', $precoUnitario);
        $this->precoUnitario = filter_var($precoUnitario, FILTER_VALIDATE_FLOAT);
        if ($this->precoUnitario === false || $this->precoUnitario < 0) {
            $this->erros[] = "O preço unitário deve ser um valor numérico maior ou igual a zero.";
        }

        // 4. Validação do Preço de Custo (DECIMAL 10,2)
        $precoCusto = str_replace(',', '.', $precoCusto);
        $this->precoCusto = filter_var($precoCusto, FILTER_VALIDATE_FLOAT);
        if ($this->precoCusto === false || $this->precoCusto < 0) {
            $this->erros[] = "O preço de custo deve ser um valor numérico maior ou igual a zero.";
        }

        // Regra de negócio extra: Custo não deve ser maior que a venda (opcional, remova se não quiser)
        if ($this->precoCusto > $this->precoUnitario) {
            $this->erros[] = "Aviso: O preço de custo não pode ser maior que o preço de venda.";
        }

        // 5. Validação do SKU (Opcional, mas Único)
        $this->sku = trim($sku);
        if (empty($this->sku)) {
            $this->sku = null; // Envia como NULL para o banco se não for preenchido
        } else {
            $this->sku = strtoupper(preg_replace('/[^A-Za-z0-9_-]/', '', $this->sku)); // Limpa caracteres especiais
        }

        // 6. Validação do Código de Barras (Opcional, mas Único)
        $this->codigoBarras = trim($codigoBarras);
        if (empty($this->codigoBarras)) {
            $this->codigoBarras = null;
        } else {
            $this->codigoBarras = preg_replace('/[^0-9]/', '', $this->codigoBarras); // Mantém apenas números
            if (strlen($this->codigoBarras) > 15) {
                $this->erros[] = "O código de barras não pode ter mais que 15 dígitos.";
            }
        }

        // 7. Validação da Unidade de Medida (CHAR 3)
       $this->unidade = strtoupper(trim($unidade));

        if (empty($this->unidade)) {
        $this->unidade = 'UN'; // Valor padrão
        } elseif (mb_strlen($this->unidade, 'UTF-8') > 3){ // Conta caracteres reais, ignorando acentos
        $this->erros[] = "A unidade de medida deve conter no máximo 3 caracteres (Ex: UN, PC, PAR, JG, CX, MT).";
        }

        return $this->erros;
    }

    // ==========================================
    // GETTERS
    // ==========================================
    public function getNome() { return $this->nome; }
    public function getQuantEstoque() { return $this->quantEstoque; }
    public function getPrecoUnitario() { return $this->precoUnitario; }
    public function getPrecoCusto() { return $this->precoCusto; }
    public function getSku() { return $this->sku; }
    public function getCodigoBarras() { return $this->codigoBarras; }
    public function getUnidade() { return $this->unidade; }
}