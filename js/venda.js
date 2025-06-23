document.addEventListener('DOMContentLoaded', function() {
    const listaProdutos = document.getElementById('lista-produtos-venda');
    const resumoVenda = document.getElementById('resumo-venda');
    const totalVenda = document.getElementById('total-venda');
  
    let venda = [];
  
    document.getElementById('btn-buscar-produto').addEventListener('click', function() {
      const termo = document.getElementById('busca-produto-venda').value;

      fetch('buscar_produtos.php?termo=' + encodeURIComponent(termo))
        .then(response => response.json())
        .then(data => {
          listaProdutos.innerHTML = '';
  
          data.forEach(produto => {
            listaProdutos.innerHTML += `
              <tr>
                <td>${produto.nome}</td>
                <td>${produto.estoque}</td>
                <td>R$ ${parseFloat(produto.preco).toFixed(2)}</td>
                <td><input type="number" class="form-control" id="qtd-${produto.id}" value="1" min="1" max="${produto.estoque}"></td>
                <td>
                  <button class="btn btn-primary btn-sm" onclick="adicionarProduto(${produto.id}, '${produto.nome}', ${produto.preco}, ${produto.estoque})">
                    Adicionar
                  </button>
                </td>
              </tr>
            `;
          });
        });
    });
  
    window.adicionarProduto = function(id, nome, preco, estoque) {
      const qtd = parseInt(document.getElementById(`qtd-${id}`).value);
  
      if (isNaN(qtd) || qtd <= 0) {
        alert('Quantidade inválida.');
        return;
      }
  
      if (qtd > estoque) {
        alert('Quantidade maior que o estoque disponível.');
        return;
      }
  
      venda.push({ id, nome, preco, qtd });
      atualizarResumo();
    };
  
    function atualizarResumo() {
      resumoVenda.innerHTML = '';
      let totalGeral = 0;
  
      venda.forEach(item => {
        const totalItem = item.preco * item.qtd;
        totalGeral += totalItem;
  
        resumoVenda.innerHTML += `
          <tr>
            <td>${item.nome}</td>
            <td>${item.qtd}</td>
            <td>R$ ${totalItem.toFixed(2)}</td>
          </tr>
        `;
      });
  
      totalVenda.textContent = `R$ ${totalGeral.toFixed(2)}`;
    }
  
    document.getElementById('btn-cancelar-venda').addEventListener('click', function() {
      if (confirm('Tem certeza que deseja cancelar a venda?')) {
        venda = [];
        atualizarResumo();
      }
    });
  });