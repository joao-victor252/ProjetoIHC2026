// ============================================================
//  script.js — Lógica do Cadastro de Produto PDV
//  Loja de Instrumentos Musicais
// ============================================================


// ===== ARRAY QUE ARMAZENA OS PRODUTOS DA SESSÃO =====
// Cada produto é um objeto { sku, nome, categoria, precoVenda, estoque, ... }
let produtos = [];


// ============================================================
//  FUNÇÃO: calcularMargem
//  Calcula automaticamente a margem de lucro (%) sempre que
//  o usuário digita nos campos Preço de Custo ou Preço de Venda.
// ============================================================
function calcularMargem() {

  // Lê os valores dos inputs (parseFloat converte string para número)
  const custo = parseFloat(document.getElementById('precoCusto').value);
  const venda = parseFloat(document.getElementById('precoVenda').value);

  // Campo de exibição da margem
  const campoMargem = document.getElementById('margem');

  // Só calcula se ambos os valores forem números válidos e positivos
  if (!isNaN(custo) && !isNaN(venda) && venda > 0) {

    // Fórmula de margem sobre o preço de venda: ((V - C) / V) * 100
    const margem = ((venda - custo) / venda) * 100;

    // Exibe com duas casas decimais e símbolo de percentual
    campoMargem.value = margem.toFixed(2) + '%';

    // Aplica cor vermelha se margem for negativa (custo > venda)
    if (margem < 0) {
      campoMargem.style.color = '#e63946';
    } else {
      campoMargem.style.color = ''; // Retorna à cor padrão (verde escuro via CSS)
    }

  } else {
    // Se os campos estiverem incompletos, limpa o campo
    campoMargem.value = '';
  }
}


// ============================================================
//  FUNÇÃO: cadastrarProduto
//  Chamada ao submeter o formulário (evento onsubmit).
//  Coleta os dados dos campos, valida e adiciona à tabela.
// ============================================================
function cadastrarProduto(evento) {

  // Impede o comportamento padrão do form (recarregar a página)
  evento.preventDefault();

  // ---- Coleta dos valores de cada campo ----
  const produto = {
    codigoBarras : document.getElementById('codigoBarras').value.trim(),
    sku          : document.getElementById('sku').value.trim().toUpperCase(),
    nome         : document.getElementById('nomeProduto').value.trim(),
    categoria    : document.getElementById('categoria').value,
    marca        : document.getElementById('marca').value.trim(),
    precoCusto   : parseFloat(document.getElementById('precoCusto').value) || 0,
    precoVenda   : parseFloat(document.getElementById('precoVenda').value),
    estoque      : parseInt(document.getElementById('estoque').value),
    estoqueMin   : parseInt(document.getElementById('estoqueMin').value) || 0,
    localizacao  : document.getElementById('localizacao').value.trim(),
    descricao    : document.getElementById('descricao').value.trim(),
  };

  // ---- Validação extra: SKU duplicado ----
  // Verifica se já existe um produto com o mesmo SKU na sessão
  const skuExiste = produtos.some(p => p.sku === produto.sku);
  if (skuExiste) {
    exibirMensagem('erro', `⚠ SKU "${produto.sku}" já foi cadastrado nesta sessão.`);
    document.getElementById('sku').focus(); // Leva o foco de volta ao campo SKU
    return; // Interrompe o cadastro
  }

  // ---- Adiciona o produto ao array da sessão ----
  produtos.push(produto);

  // ---- Adiciona o produto na tabela visual ----
  adicionarLinhaTabela(produto);

  // ---- Exibe mensagem de sucesso ----
  exibirMensagem('sucesso', `✓ Produto "${produto.nome}" cadastrado com sucesso!`);

  // ---- Limpa o formulário para o próximo cadastro ----
  limparFormulario();
}


// ============================================================
//  FUNÇÃO: adicionarLinhaTabela
//  Insere uma nova linha na tabela de produtos cadastrados
//  e garante que a seção da tabela fique visível.
// ============================================================
function adicionarLinhaTabela(produto) {

  // Exibe a seção da tabela (estava oculta com display:none)
  document.getElementById('secaoLista').style.display = 'block';

  // Referência ao <tbody> da tabela
  const corpo = document.getElementById('corpoTabela');

  // Cria um novo elemento <tr> (linha)
  const linha = document.createElement('tr');

  // Formata o preço de venda em Real Brasileiro
  const precoFormatado = produto.precoVenda.toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  });

  // Formata a categoria: troca o valor do option pelo texto legível
  const categoriaTexto = obterTextoCategoria(produto.categoria);

  // Preenche as células (td) da linha com os dados do produto
  linha.innerHTML = `
    <td><strong>${produto.sku}</strong></td>
    <td>${produto.nome}</td>
    <td>${categoriaTexto}</td>
    <td>${precoFormatado}</td>
    <td>${produto.estoque} un.</td>
    <td>
      <!-- Botão de remover: passa o SKU para identificar qual linha apagar -->
      <button class="btn-remover" onclick="removerProduto('${produto.sku}', this)">
        Remover
      </button>
    </td>
  `;

  // Adiciona a linha ao final do corpo da tabela
  corpo.appendChild(linha);
}


// ============================================================
//  FUNÇÃO: removerProduto
//  Remove um produto do array e da tabela pelo SKU.
//  Parâmetros:
//    sku  — identificador único do produto
//    btn  — referência ao botão clicado (para encontrar a linha)
// ============================================================
function removerProduto(sku, btn) {

  // Remove do array de produtos (filter cria novo array sem o item)
  produtos = produtos.filter(p => p.sku !== sku);

  // Remove a linha da tabela navegando do botão → <td> → <tr>
  const linha = btn.closest('tr');
  linha.remove();

  // Se não houver mais produtos, esconde a seção da tabela novamente
  if (produtos.length === 0) {
    document.getElementById('secaoLista').style.display = 'none';
  }
}


// ============================================================
//  FUNÇÃO: limparFormulario
//  Reseta todos os campos do formulário para seus valores padrão.
//  Chamada pelo botão "Limpar" e automaticamente após salvar.
// ============================================================
function limparFormulario() {

  // reset() é um método nativo do HTMLFormElement que limpa todos os campos
  document.getElementById('formProduto').reset();

  // Limpa também o campo de margem (que não faz parte do reset padrão por ser readonly)
  document.getElementById('margem').value = '';
  document.getElementById('margem').style.color = '';

  // Coloca o foco no primeiro campo para agilizar o próximo cadastro
  document.getElementById('sku').focus();
}


// ============================================================
//  FUNÇÃO: exibirMensagem
//  Exibe uma mensagem de feedback acima dos botões.
//  Parâmetros:
//    tipo    — 'sucesso' ou 'erro' (define a classe CSS aplicada)
//    texto   — mensagem a ser exibida
// ============================================================
function exibirMensagem(tipo, texto) {

  const div = document.getElementById('mensagem');

  // Define o texto e a classe visual (sucesso = verde, erro = vermelho)
  div.textContent = texto;
  div.className = `mensagem ${tipo}`; // Ex: "mensagem sucesso" ou "mensagem erro"
  div.style.display = 'block'; // Torna o elemento visível

  // Esconde a mensagem automaticamente após 4 segundos
  clearTimeout(div._timeout); // Cancela timer anterior se houver
  div._timeout = setTimeout(() => {
    div.style.display = 'none';
  }, 4000);
}


// ============================================================
//  FUNÇÃO AUXILIAR: obterTextoCategoria
//  Converte o value do <select> no texto legível correspondente.
//  Parâmetro:
//    valor — value da opção selecionada
// ============================================================
function obterTextoCategoria(valor) {
  // Mapa de value → texto legível
  const mapa = {
    'cordas'        : 'Cordas',
    'teclas'        : 'Teclas e Piano',
    'sopros'        : 'Sopros',
    'percussao'     : 'Percussão',
    'acessorios'    : 'Acessórios',
    'amplificadores': 'Amplificadores',
    'estudio'       : 'Estúdio e Gravação',
  };

  // Retorna o texto ou o próprio valor caso não esteja no mapa
  return mapa[valor] || valor;
}
