
<?php

$current = basename($_SERVER['SCRIPT_NAME']);
function isActive(string $page, string $current): string {
    return $page === $current ? 'active' : '';
}
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>


<div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
  <div class="position-sticky pt-3 vh-100 d-flex flex-column">
   
    <div class="sidebar-header text-center mb-4 px-3">
      <h1 class="h4 text-white">GESTORMAX</h1>
    </div>

    <nav class="flex-grow-1">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link text-white <?php echo isActive('dashboard.php', $current); ?>" href="dashboard.php">
            <i class="bi bi-house-door me-2"></i>
            Início
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white <?php echo isActive('produtos-lista.php', $current); ?>" href="produtos-lista.php">
            <i class="bi bi-box-seam me-2"></i>
            Produtos
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white <?php echo isActive('produtos-cadastro.php', $current); ?>" href="produtos-cadastro.php">
            <i class="bi bi-plus-circle me-2"></i>
            Cadastrar produto
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white <?php echo isActive('criar-categoria.php', $current); ?>" href="criar-categoria.php">
            <i class="bi bi-house-door me-2"></i>
            Cadastrar categoria
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white <?php echo isActive('vendas.php', $current); ?>" href="vendas.php">
            <i class="bi bi-cart me-2"></i>
            Vendas
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white <?php echo isActive('usuarios-lista.php', $current); ?>" href="usuarios-lista.php">
            <i class="bi bi-people me-2"></i>
            Usuários
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white <?php echo isActive('estatisticas.php', $current); ?>" href="estatisticas.php">
            <i class="bi bi-bar-chart-line me-2"></i>
            Estatísticas
          </a>
        </li>
      </ul>
    </nav>

    <div class="sidebar-footer mt-auto p-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-white" id="user-name">
        <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Administrador'); ?>
        </span>
      </div>
      <a href="logout.php" class="btn btn-outline-light w-100">
         <i class="bi bi-box-arrow-right me-2"></i> Sair
      </a>
      </button>
    </div>
  </div>
</div>
