<?php
session_start();
include 'conexao.php';

// Ação ativar/desativar via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'], $_POST['idname'])) {
    $id = intval($_POST['idname']);
    if ($id > 0) {
        if ($_POST['acao'] === 'ativar') {
            $stmt = $conn->prepare("UPDATE usuarios SET status=1 WHERE idname = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $_SESSION['msg'] = "<div class='alert alert-success'>Usuário ativado com sucesso!</div>";
        } elseif ($_POST['acao'] === 'desativar') {
            $stmt = $conn->prepare("UPDATE usuarios SET status=0 WHERE idname = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $_SESSION['msg'] = "<div class='alert alert-warning'>Usuário desativado com sucesso!</div>";
        }
    } else {
        $_SESSION['msg'] = "<div class='alert alert-danger'>ID inválido.</div>";
    }
    header("Location: usuarios-lista.php");
    exit;
}

// Paginação
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 8;
$offset = ($page -1)* $itemsPerPage;

$sqlCount = "SELECT COUNT(*) as total FROM usuarios";
$resultCount = $conn->query($sqlCount);
$rowCount = $resultCount->fetch_assoc();
$totalUsuarios = intval($rowCount['total']);
$totalPages = ceil($totalUsuarios / $itemsPerPage);

// Busca usuários paginados
$sql = "SELECT * FROM usuarios ORDER BY name LIMIT $itemsPerPage OFFSET $offset";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>GESTORMAX - Lista de Usuários</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="../styles/usuarios.css" />
  <link rel="stylesheet" href="../styles/sidebar.css" />
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <?php include '../INCLUDE/sidebar.php'; ?>
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div
          class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
        >
          <h1 class="h2">Lista de Usuários</h1>
          <div class="btn-toolbar mb-2 mb-md-0">
            <a href="criar-usuario.php" class="btn btn-primary"
              ><i class="bi bi-plus-circle me-1"></i> Novo Usuário</a
            >
          </div>
        </div>

        <!-- Mensagem -->
        <?php
          if (!empty($_SESSION['msg'])) {
              echo $_SESSION['msg'];
              unset($_SESSION['msg']);
          }
        ?>

        <!-- Busca -->
        <div class="input-group mb-3">
          <input
            type="text"
            id="busca-usuario"
            class="form-control"
            placeholder="Buscar usuário..."
            aria-label="Buscar usuário"
          />
          <button class="btn btn-outline-secondary" type="button" id="btn-buscar-usuario">
            <i class="bi bi-search"></i>
          </button>
        </div>

        <!-- Tabela -->
        <div class="table-responsive">
          <table class="table table-hover" id="tabela-usuarios">
            <thead class="table-light">
              <tr>
                <th>Nome</th>
                <th>Usuário</th>
                <th>E-mail</th>
                <th>Perfil</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['nickname']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['email']) . "</td>";

                      echo "<td>";
                      if ($row['typePerfil'] == '1') {
                          echo "<span class='badge bg-primary'>Administrador</span>";
                      } else {
                          echo "<span class='badge bg-info text-dark'>Vendedor</span>";
                      }
                      echo "</td>";

                      echo "<td>";
                      if ($row['status'] == '1') {
                          echo "<span class='badge bg-success'>Ativo</span>";
                      } else {
                          echo "<span class='badge bg-secondary'>Inativo</span>";
                      }
                      echo "</td>";

                      echo "<td>";
                      echo "<a href='editar-usuario.php?idname=" . $row['idname'] . "' class='btn btn-sm btn-outline-primary' title='Editar'><i class='bi bi-pencil'></i></a> ";

                      // Botão ativar/desativar com formulário oculto
                      if ($row['status'] == '1') {
                          // Desativar
                          echo "
                          <form method='POST' style='display:inline;' onsubmit='return confirm(\"Deseja desativar este usuário?\");'>
                            <input type='hidden' name='idname' value='" . $row['idname'] . "' />
                            <input type='hidden' name='acao' value='desativar' />
                            <button type='submit' class='btn btn-sm btn-outline-danger' title='Desativar'>
                              <i class='bi bi-person-x'></i>
                            </button>
                          </form>";
                      } else {
                          // Ativar
                          echo "
                          <form method='POST' style='display:inline;' onsubmit='return confirm(\"Deseja ativar este usuário?\");'>
                            <input type='hidden' name='idname' value='" . $row['idname'] . "' />
                            <input type='hidden' name='acao' value='ativar' />
                            <button type='submit' class='btn btn-sm btn-outline-success' title='Ativar'>
                              <i class='bi bi-person-check'></i>
                            </button>
                          </form>";
                      }
                      echo "</td>";

                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='6' class='text-center'>Nenhum usuário encontrado.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>

        <!-- Paginação -->
        <?php if ($totalUsuarios > $itemsPerPage): ?>
          <nav aria-label="Navegação de páginas">
            <ul class="pagination justify-content-center">
              <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= max($page - 1, 1) ?>" tabindex="-1">Anterior</a>
              </li>

              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>

              <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= min($page + 1, $totalPages) ?>">Próxima</a>
              </li>
            </ul>
          </nav>
        <?php endif; ?>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const inputBusca = document.getElementById('busca-usuario');
    const tabelaUsuarios = document.getElementById('tabela-usuarios');
    const linhas = tabelaUsuarios.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    inputBusca.addEventListener('input', function () {
      const textoBusca = this.value.toLowerCase();

      for (let i = 0; i < linhas.length; i++) {
        const linha = linhas[i];
        const textoLinha = linha.textContent.toLowerCase();

        if (textoLinha.indexOf(textoBusca) > -1) {
          linha.style.display = '';
        } else {
          linha.style.display = 'none';
        }
      }
    });

    // Botão só foca no input, pois busca é em tempo real
    document.getElementById('btn-buscar-usuario').addEventListener('click', () => {
      inputBusca.focus();
    });
  </script>
</body>
</html>
