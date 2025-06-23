<?php

include 'conexao.php';
include 'autenticacao.php';

proteger_pagina();

// Total de produtos
$sqlTotal = "SELECT COUNT(*) AS total FROM produtos";
$resTotal = $conn->query($sqlTotal);
$totalProdutos = $resTotal->fetch_assoc()['total'] ?? 0;

// Valor do estoque (soma de custo * quantidade_inicial)
$sqlValorEstoque = "SELECT SUM(custo * quantidade_inicial) AS valor_estoque FROM produtos";
$resValorEstoque = $conn->query($sqlValorEstoque);
$valorEstoque = $resValorEstoque->fetch_assoc()['valor_estoque'] ?? 0;

// Lucro potencial (soma do (valor_venda - custo) * quantidade_inicial)
$sqlLucroPotencial = "SELECT SUM((valor_venda - custo) * quantidade_inicial) AS lucro_potencial FROM produtos";
$resLucroPotencial = $conn->query($sqlLucroPotencial);
$lucroPotencial = $resLucroPotencial->fetch_assoc()['lucro_potencial'] ?? 0;

// Estoque baixo (quantos produtos estão com quantidade_inicial < quantidade_minima)
$sqlEstoqueBaixo = "SELECT COUNT(*) AS estoque_baixo FROM produtos WHERE quantidade_inicial < quantidade_minima";
$resEstoqueBaixo = $conn->query($sqlEstoqueBaixo);
$estoqueBaixo = $resEstoqueBaixo->fetch_assoc()['estoque_baixo'] ?? 0;

// Top 5 produtos mais lucrativos (ordena por margem: (valor_venda - custo) desc)
$sqlTopLucrativos = "SELECT nome, quantidade_inicial, custo, valor_venda, 
    ((valor_venda - custo) * quantidade_inicial) AS lucro_total
    FROM produtos
    ORDER BY lucro_total DESC
    LIMIT 5";
$resTopLucrativos = $conn->query($sqlTopLucrativos);

// Produtos com estoque crítico (quantidade_inicial < quantidade_minima)
$sqlCriticos = "SELECT nome, quantidade_inicial, quantidade_minima FROM produtos WHERE quantidade_inicial < quantidade_minima";
$resCriticos = $conn->query($sqlCriticos);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>GESTORMAX - Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="../styles/dashboard.css" />
  <link rel="stylesheet" href="../styles/sidebar.css" />
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <?php include '../INCLUDE/sidebar.php'; ?>

      <!-- Main content -->
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div
          class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
        >
          <h1 class="h2">Painel de Controle</h1>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card stat-card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h3 class="h6 text-muted mb-0">Total de Produtos</h3>
                  <i class="bi bi-box text-primary fs-4"></i>
                </div>
                <div class="stat-value" id="total-produtos"><?= $totalProdutos ?></div>
                <p class="text-muted mb-0 small">produtos cadastrados</p>
              </div>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card stat-card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h3 class="h6 text-muted mb-0">Valor do Estoque</h3>
                  <i class="bi bi-currency-dollar text-success fs-4"></i>
                </div>
                <div class="stat-value" id="valor-estoque">R$ <?= number_format($valorEstoque, 2, ',', '.') ?></div>
                <p class="text-muted mb-0 small">em produtos</p>
              </div>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card stat-card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h3 class="h6 text-muted mb-0">Lucro Potencial</h3>
                  <i class="bi bi-gem text-warning fs-4"></i>
                </div>
                <div class="stat-value" id="lucro-potencial">R$ <?= number_format($lucroPotencial, 2, ',', '.') ?></div>
                <p class="text-muted mb-0 small">estimado</p>
              </div>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card stat-card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h3 class="h6 text-muted mb-0">Estoque Baixo</h3>
                  <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                </div>
                <div class="stat-value" id="estoque-baixo"><?= $estoqueBaixo ?></div>
                <p class="text-muted mb-0 small">produtos</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Info Cards -->
        <div class="row">
          <div class="col-lg-6 mb-4">
            <div class="card h-100">
              <div class="card-header">
                <h2 class="h5 card-title mb-1">Produtos Mais Lucrativos</h2>
                <p class="text-muted small mb-0">Top 5 produtos com maior margem de lucro</p>
              </div>
              <div class="card-body">
                <ul class="list-group list-group-flush" id="produtos-lucrativos">
                  <?php
                  if ($resTopLucrativos && $resTopLucrativos->num_rows > 0) {
                      while ($prod = $resTopLucrativos->fetch_assoc()) {
                          echo "<li class='list-group-item'>";
                          echo htmlspecialchars($prod['nome']) . " - Lucro: R$ " . number_format($prod['lucro_total'], 2, ',', '.');
                          echo "</li>";
                      }
                  } else {
                      echo "<li class='list-group-item text-muted fst-italic'>Nenhum produto cadastrado</li>";
                  }
                  ?>
                </ul>
              </div>
            </div>
          </div>
          
          <div class="col-lg-6 mb-4">
            <div class="card h-100">
              <div class="card-header">
                <h2 class="h5 card-title mb-1">Estoque Crítico</h2>
                <p class="text-muted small mb-0">Produtos com estoque abaixo do mínimo</p>
              </div>
              <div class="card-body">
                <ul class="list-group list-group-flush" id="produtos-criticos">
                  <?php
                  if ($resCriticos && $resCriticos->num_rows > 0) {
                      while ($prod = $resCriticos->fetch_assoc()) {
                          echo "<li class='list-group-item'>";
                          echo htmlspecialchars($prod['nome']) . " - Estoque: " . intval($prod['quantidade_inicial']) . " / Mínimo: " . intval($prod['quantidade_minima']);
                          echo "</li>";
                      }
                  } else {
                      echo "<li class='list-group-item text-muted fst-italic'>Nenhum produto com estoque crítico</li>";
                  }
                  ?>
                </ul>
              </div>
              <div class="card-footer bg-transparent">
                <a href="produtos-lista.php" class="btn btn-primary float-end">Gerenciar Estoque</a>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
