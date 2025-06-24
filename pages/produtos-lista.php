<?php
include 'conexao.php';
session_start();

// Exclusão via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'excluir_produto') {
    $idExcluir = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($idExcluir > 0) {
        $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
        $stmt->bind_param("i", $idExcluir);
        if ($stmt->execute()) {
            $_SESSION['msg'] = "Produto excluído com sucesso!";
        } else {
            $_SESSION['msg'] = "Erro ao excluir o produto.";
        }
    } else {
        $_SESSION['msg'] = "ID inválido para exclusão.";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Configuração da paginaçãoAdd commentMore actions
$itemsPerPage = 7; // Itens por página
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Página atual
$offset = ($page - 1) * $itemsPerPage; // Cálculo do offset

// Consulta para contar o total de produtos
$sqlCount = "SELECT COUNT(*) AS total FROM produtos";
$resultCount = $conn->query($sqlCount);
$totalProdutos = $resultCount->fetch_assoc()['total'];
$totalPages = ceil($totalProdutos / $itemsPerPage);

// Consulta principal com paginação
$sql = "SELECT p.*, c.nome AS cat_nome FROM produtos p 
        LEFT JOIN categorias c ON p.categoria_id = c.categoria_idAdd commentMore actions
        LIMIT $offset, $itemsPerPage";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>GESTORMAX - Lista de Produtos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="../styles/produtos.css" />
  <link rel="stylesheet" href="../styles/sidebar.css" />
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <?php include '../INCLUDE/sidebar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">

      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Lista de Produtos</h1>
        <a href="produtos-cadastro.php" class="btn btn-primary">
          <i class="bi bi-plus-circle me-1"></i> Novo Produto
        </a>
      </div>

      <?php
      if (isset($_SESSION['msg'])) {
          echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['msg']) . '</div>';
          unset($_SESSION['msg']);
      }
      ?>

      <div class="card shadow-sm">
        <div class="card-body">
          <div class="input-group mb-3">
            <input type="text" id="busca-produto" class="form-control" placeholder="Buscar produto..." />
            <button class="btn btn-outline-secondary" type="button" id="btn-buscar-produto">
              <i class="bi bi-search"></i>
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-hover" id="tabela-produtos">
              <thead class="table-light">
                <tr>
                  <th>Nome</th>
                  <th>Categoria</th>
                  <th>Qtd Inicial</th>
                  <th>Qtd Mínima</th>
                  <th>Custo</th>
                  <th>Valor de Venda</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['cat_nome'] ?? 'Sem categoria') . "</td>";
                    echo "<td>" . intval($row['quantidade_inicial']) . "</td>";
                    echo "<td>" . intval($row['quantidade_minima']) . "</td>";
                    echo "<td>R$ " . number_format($row['custo'], 2, ',', '.') . "</td>";
                    echo "<td>R$ " . number_format($row['valor_venda'], 2, ',', '.') . "</td>";
                    echo "<td>
                            <a href='produtos-editar.php?id=" . $row['id'] . "' class='btn btn-sm btn-outline-primary' title='Editar'>
                              <i class='bi bi-pencil'></i>
                            </a>
                            <button class='btn btn-sm btn-outline-danger btn-excluir' data-id='" . $row['id'] . "' title='Excluir'>
                              <i class='bi bi-trash'></i>
                            </button>
                          </td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='7' class='text-center'>Nenhum produto encontrado.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </main>
  </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmar Ação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        Tem certeza que deseja excluir este produto?
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-danger" id="btn-confirm-delete">Excluir</button>
      </div>
    </div>
  </div>
</div>

<form id="form-excluir-produto" method="POST" style="display:none;">
  <input type="hidden" name="acao" value="excluir_produto" />
  <input type="hidden" name="id" id="idExcluir" value="" />
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Modal exclusão
  const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
  let produtoIdParaExcluir = null;

  document.querySelectorAll('.btn-excluir').forEach(button => {
    button.addEventListener('click', () => {
      produtoIdParaExcluir = button.getAttribute('data-id');
      confirmModal.show();
    });
  });

  document.getElementById('btn-confirm-delete').addEventListener('click', () => {
    if(produtoIdParaExcluir) {
      document.getElementById('idExcluir').value = produtoIdParaExcluir;
      document.getElementById('form-excluir-produto').submit();
    }
  });

  // Busca em tempo real
  document.getElementById('busca-produto').addEventListener('input', function() {
    const busca = this.value.toLowerCase();
    const linhas = document.querySelectorAll('#tabela-produtos tbody tr');
    linhas.forEach(linha => {
      const textoLinha = linha.textContent.toLowerCase();
      if (textoLinha.indexOf(busca) > -1) {
        linha.style.display = '';
      } else {
        linha.style.display = 'none';
      }
    });
  });

  // Opcional: botao busca só faz foco no input (pq a busca é em tempo real)
  document.getElementById('btn-buscar-produto').addEventListener('click', () => {
    document.getElementById('busca-produto').focus();
  });
</script>

</body>
</html>
