<?php
session_start();
include 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nickname = $_POST['nickname'] ?? '';
  $senha = $_POST['senha'] ?? '';

  $stmt = $conn->prepare("SELECT * FROM usuarios WHERE nickname = ? AND status = 1");
  $stmt->bind_param("s", $nickname);
  $stmt->execute();
  $resultado = $stmt->get_result();

  if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    if (password_verify($senha, $usuario['senha'])) {
      $_SESSION['usuario_id'] = $usuario['idname'];
      $_SESSION['usuario_nome'] = $usuario['name'];
      $_SESSION['usuario_tipo'] = $usuario['typePerfil'];

      header("Location: dashboard.php");
      exit;
    } else {
      $erro = "Senha incorreta.";
    }
  } else {
    $erro = "Usuário não encontrado ou inativo.";
  }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>GESTORMAX - Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"/>
</head>

<body class="bg-light">
  <div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
      <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card shadow-lg">
          <div class="card-header bg-primary text-white text-center py-4">
            <h1 class="h3 mb-1 fw-bold">GESTORMAX</h1>
            <p class="mb-0 opacity-75">Sistema de Controle de Estoque</p>
          </div>
          <div class="card-body p-4 p-md-5">
            <?php if ($erro): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <form method="POST" action="login.php">
              <div class="mb-4">
                <label for="nickname" class="form-label">Usuário</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                  <input type="text" class="form-control form-control-lg" id="nickname" name="nickname" placeholder="Digite seu usuário" required />
                </div>
              </div>

              <div class="mb-4">
                <label for="senha" class="form-label">Senha</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                  <input type="password" class="form-control form-control-lg" id="senha" name="senha" placeholder="Digite sua senha" required />
                </div>
              </div>

              <button type="submit" class="btn btn-primary btn-lg w-100 py-2 mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
              </button>

              <a href="index.php" class="btn btn-secondary btn-lg w-100 py-2">
                <i class="bi bi-arrow-left me-2"></i>Voltar
              </a>
            </form>
          </div>
          <div class="card-footer text-center py-3 bg-light">
            <p class="mb-0 text-muted small">© 2025 GESTORMAX - Todos os direitos reservados</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
