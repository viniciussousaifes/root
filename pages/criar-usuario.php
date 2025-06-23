<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conexao.php';

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['nome'];
    $email = $_POST['email'];
    $confirmar_email = $_POST['confirmar_email'];
    $nickname = $_POST['usuario'];
    $typePerfil = $_POST['perfil'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if ($email != $confirmar_email) {
        $mensagem = "<div class='alert alert-danger'>Os e-mails não coincidem.</div>";
    } elseif ($senha != $confirmar_senha) {
        $mensagem = "<div class='alert alert-danger'>As senhas não coincidem.</div>";
    } else {
      $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

      $sql = "INSERT INTO usuarios (name, nickname, email, typePerfil, senha, status) 
              VALUES ('$name', '$nickname', '$email', '$typePerfil', '$senha_hash', '1')";

        if ($conn->query($sql) === TRUE) {
            $mensagem = "<div class='alert alert-success'>Usuário cadastrado com sucesso!</div>";
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GESTORMAX - Criar Usuário</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../styles/usuarios.css">
<link rel="stylesheet" href="../styles/sidebar.css">
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <?php include '../INCLUDE/sidebar.php'; ?>

      <!-- Main content -->
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">Criar Novo Usuário</h1>
        </div>

        <!-- Mensagem -->
        <?php if (isset($mensagem)) echo $mensagem; ?>

        <div class="card shadow-sm">
          <div class="card-header bg-light">
            <h2 class="h5 card-title mb-1">Informações do Usuário</h2>
            <p class="text-muted small mb-0">Preencha os dados do novo usuário</p>
          </div>
          <div class="card-body">
            <form method="POST">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="nome" class="form-label">Nome Completo</label>
                  <input type="text" class="form-control" id="nome" name="nome" required>
                </div>

                <div class="col-md-6">
                  <label for="email" class="form-label">E-mail</label>
                  <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="col-md-6">
                  <label for="confirmar_email" class="form-label">Confirmar e-mail</label>
                  <input type="email" class="form-control" id="confirmar_email" name="confirmar_email" required>
                </div>

                <div class="col-md-6">
                  <label for="usuario" class="form-label">Nome de Usuário</label>
                  <input type="text" class="form-control" id="usuario" name="usuario" required>
                </div>

                <div class="col-md-6">
                  <label for="perfil" class="form-label">Perfil</label>
                  <select class="form-select" id="perfil" name="perfil" required>
                    <option value="1">Administrador</option>
                    <option value="0">Vendedor</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="senha" class="form-label">Senha</label>
                  <input type="password" class="form-control" id="senha" name="senha" required>
                </div>

                <div class="col-md-6">
                  <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                  <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                </div>
              </div>

              <div class="d-flex justify-content-end mt-4 gap-2">
                <button type="button" id="btn-cancelar" class="btn btn-outline-secondary" onclick="location.href='usuarios-lista.php'">Cancelar</button>
                <button type="submit" class="btn btn-primary">Cadastrar Usuário</button>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
