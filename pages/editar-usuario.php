<?php
include 'conexao.php';

if (!$conn) {
  die("Conexão com o banco falhou.");
}

$mensagem = '';

$id = isset($_GET['idname']) ? intval($_GET['idname']) : 0;

if ($id <= 0) {
    header("Location: usuarios-lista.php");
    exit;
}

$sql = "SELECT * FROM usuarios WHERE idname = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit;
}

// Atualização
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $confirmar_email = $_POST['confirmar_email'];
  $nickname = $_POST['nickname'];
  $typePerfil = $_POST['typePerfil'];
  $senha = $_POST['senha'];
  $confirmar_senha = $_POST['confirmar_senha'];
  $status = isset($_POST['status']) ? 1 : 0;

  if ($email !== $confirmar_email) {
      $mensagem = '<div class="alert alert-danger">Os e-mails não coincidem.</div>';
  } elseif (!empty($senha) && $senha !== $confirmar_senha) {
      $mensagem = '<div class="alert alert-danger">As senhas não coincidem.</div>';
  } else {
      if (!empty($senha)) {
          $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
          $sql = "UPDATE usuarios SET name=?, email=?, nickname=?, typePerfil=?, senha=?, status=? WHERE idname=?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("ssssssi", $name, $email, $nickname, $typePerfil, $senha_hash, $status, $id);
      } else {
          $sql = "UPDATE usuarios SET name=?, email=?, nickname=?, typePerfil=?, status=? WHERE idname=?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("sssisi", $name, $email, $nickname, $typePerfil, $status, $id);
      }

      if ($stmt->execute()) {
          $mensagem = '<div class="alert alert-success">Usuário atualizado com sucesso!</div>';
      } else {
          $mensagem = '<div class="alert alert-danger">Erro ao atualizar o usuário.</div>';
      }
  }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>GESTORMAX - Editar Usuário</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="styles/usuarios.css">
  <link rel="stylesheet" href="../styles/sidebar.css">
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <?php include '../INCLUDE/sidebar.php'; ?>

      <!-- Conteúdo principal -->
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">Editar Usuário</h1>
          <div class="btn-toolbar mb-2 mb-md-0">
            <a href="usuarios-lista.php" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
          </div>
        </div>

        <?= $mensagem ?>

        <form method="POST" class="card shadow-sm p-4">
          <input type="hidden" name="id" value="<?= $id ?>">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nome Completo</label>
              <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($usuario['name']) ?>">
            </div>

            <div class="col-md-6">
              <label class="form-label">E-mail</label>
              <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($usuario['email']) ?>">
            </div>

            <div class="col-md-6">
              <label class="form-label">Confirmar E-mail</label>
              <input type="email" name="confirmar_email" class="form-control" required value="<?= htmlspecialchars($usuario['email']) ?>">
            </div>

            <div class="col-md-6">
              <label class="form-label">Nome de Usuário</label>
              <input type="text" name="nickname" class="form-control" required value="<?= htmlspecialchars($usuario['nickname']) ?>">
            </div>

            <div class="col-md-6">
              <label class="form-label">Perfil</label>
              <select name="typePerfil" class="form-select" required>
                <option value="1" <?= $usuario['typePerfil'] === '1' ? 'selected' : '' ?>>Administrador</option>
                <option value="0" <?= $usuario['typePerfil'] === '0' ? 'selected' : '' ?>>Vendedor</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nova Senha</label>
              <input type="password" name="senha" class="form-control" placeholder="Deixe em branco para manter">
            </div>

            <div class="col-md-6">
              <label class="form-label">Confirmar Nova Senha</label>
              <input type="password" name="confirmar_senha" class="form-control">
            </div>

            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check form-switch mt-4">
                <input class="form-check-input" type="checkbox" name="status" <?= $usuario['status'] ? 'checked' : '' ?>>
                <label class="form-check-label">Usuário Ativo</label>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end mt-4 gap-2">
            <a href="usuarios-lista.php" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
          </div>
        </form>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
