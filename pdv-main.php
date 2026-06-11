<?php 
if(session_status() === PHP_SESSION_NONE){
  session_start();
}

if (empty($_SESSION['logado']) || ($_SESSION['logado'] !== true)){
  session_unset();
  session_destroy();
  header('Location: login.php');
  exit();
}
$nome_usuario = $_SESSION['nome_usuario'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PDV - Loja de Instrumentos Musicais</title>
  <link rel="stylesheet" href="pdv-instrumentos.css">
</head>
<body>
  <header class="header">
    <h1>🎸 PDV Instrumentos Musicais</h1>
    <span class="operador">Operador: <?=$nome_usuario?></span>
    <a href="logout.php" class="btn-sair">Sair</a>
  </header>

  <main class="container">
    <section class="catalogo">
      <h2>Catálogo de Instrumentos</h2>
      <input type="text" id="busca" placeholder="Buscar instrumento..." oninput="filtrar()">
      
      <div class="lista-instrumentos" id="lista"></div>
    </section>

    <section class="carrinho">
      <h2>Carrinho</h2>
      <div id="itens-carrinho" class="itens-carrinho">
        <p class="vazio">Carrinho vazio</p>
      </div>
      <div class="total">
        <strong>Total:</strong>
        <span id="total">R$ 0,00</span>
      </div>
      <button id="finalizar" onclick="finalizarVenda()" disabled>Finalizar Venda</button>
    </section>
  </main>

  <script>
    const instrumentos = [
      { id: 1, nome: 'Guitarra Stratocaster', preco: 2499.00, tipo: 'Cordas' },
      { id: 2, nome: 'Violão Nylon Clássico', preco: 799.00, tipo: 'Cordas' },
      { id: 3, nome: 'Teclado Roland 61 Teclas', preco: 1899.00, tipo: 'Teclas' },
      { id: 4, nome: 'Bateria Acústica Pearl', preco: 3299.00, tipo: 'Percussão' },
      { id: 5, nome: 'Baixo Precision 4 Cordas', preco: 1799.00, tipo: 'Cordas' },
      { id: 6, nome: 'Ukulele Soprano', preco: 249.00, tipo: 'Cordas' },
      { id: 7, nome: 'Violino 4/4 Estudante', preco: 599.00, tipo: 'Cordas' },
      { id: 8, nome: 'Saxofone Alto', preco: 2899.00, tipo: 'Sopro' },
      { id: 9, nome: 'Amplificador 40W', preco: 699.00, tipo: 'Acessórios' },
      { id: 10, nome: 'Pedal de Efeito Delay', preco: 349.00, tipo: 'Acessórios' },
      { id: 11, nome: 'Microfone Shure SM58', preco: 599.00, tipo: 'Acessórios' },
      { id: 12, nome: 'Cajón Peruanmo', preco: 449.00, tipo: 'Percussão' }
    ];

    let carrinho = [];

    const listaEl = document.getElementById('lista');
    const carrinhoEl = document.getElementById('itens-carrinho');
    const totalEl = document.getElementById('total');
    const finalizarBtn = document.getElementById('finalizar');
    const buscaEl = document.getElementById('busca');

    function formatarPreco(valor) {
      return 'R$ ' + valor.toFixed(2).replace('.', ',');
    }

    function renderizarCatalogo(lista) {
      listaEl.innerHTML = '';
      lista.forEach(item => {
        const div = document.createElement('div');
        div.className = 'instrumento-card';
        div.innerHTML = `
          <div class="info">
            <h3>${item.nome}</h3>
            <span class="tipo">${item.tipo}</span>
            <p class="preco">${formatarPreco(item.preco)}</p>
          </div>
          <button onclick="adicionarCarrinho(${item.id})">Adicionar</button>
        `;
        listaEl.appendChild(div);
      });
    }

    function adicionarCarrinho(id) {
      const item = instrumentos.find(i => i.id === id);
      const existente = carrinho.find(i => i.id === id);
      if (existente) {
        existente.qtd++;
      } else {
        carrinho.push({ ...item, qtd: 1 });
      }
      atualizarCarrinho();
    }

    function removerCarrinho(id) {
      const idx = carrinho.findIndex(i => i.id === id);
      if (idx > -1) {
        carrinho[idx].qtd--;
        if (carrinho[idx].qtd <= 0) carrinho.splice(idx, 1);
      }
      atualizarCarrinho();
    }

    function atualizarCarrinho() {
      if (carrinho.length === 0) {
        carrinhoEl.innerHTML = '<p class="vazio">Carrinho vazio</p>';
        totalEl.textContent = formatarPreco(0);
        finalizarBtn.disabled = true;
        return;
      }

      carrinhoEl.innerHTML = '';
      let total = 0;
      carrinho.forEach(item => {
        total += item.preco * item.qtd;
        const div = document.createElement('div');
        div.className = 'item-carrinho';
        div.innerHTML = `
          <div class="item-info">
            <span class="item-nome">${item.nome}</span>
            <span class="item-preco">${formatarPreco(item.preco)} x ${item.qtd}</span>
          </div>
          <div class="item-acoes">
            <button class="btn-menor" onclick="adicionarCarrinho(${item.id})">+</button>
            <button class="btn-menor" onclick="removerCarrinho(${item.id})">-</button>
          </div>
        `;
        carrinhoEl.appendChild(div);
      });

      totalEl.textContent = formatarPreco(total);
      finalizarBtn.disabled = false;
    }

    function filtrar() {
      const termo = buscaEl.value.toLowerCase();
      const filtrados = instrumentos.filter(i =>
        i.nome.toLowerCase().includes(termo) ||
        i.tipo.toLowerCase().includes(termo)
      );
      renderizarCatalogo(filtrados);
    }

    function finalizarVenda() {
      if (carrinho.length === 0) return;
      const total = carrinho.reduce((s, i) => s + i.preco * i.qtd, 0);
      alert(`Venda finalizada!\nTotal: ${formatarPreco(total)}\nItens: ${carrinho.reduce((s, i) => s + i.qtd, 0)}`);
      carrinho = [];
      atualizarCarrinho();
    }

    renderizarCatalogo(instrumentos);
  </script>
</body>
</html>
