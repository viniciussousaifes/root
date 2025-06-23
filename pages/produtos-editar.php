<?php
include 'conexao.php';

// Verificar se foi passado um ID de produto para edição
$produto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Inicializar variáveis
$mensagem = '';
$classe_mensagem = '';
$produto = null;

// Buscar os dados do produto se existir o ID
if ($produto_id > 0) {
    $sql_produto = "SELECT * FROM produtos WHERE id = $produto_id";
    $resultado = $conn->query($sql_produto);
    
    if ($resultado->num_rows === 0) {
        $mensagem = 'Produto não encontrado!';
        $classe_mensagem = 'alert-danger';
    } else {
        $produto = $resultado->fetch_assoc();
    }
} else {
    $mensagem = 'ID do produto não informado!';
    $classe_mensagem = 'alert-danger';
}

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $produto_id > 0) {
    // Validar e sanitizar os dados
    $nome = $conn->real_escape_string(trim($_POST['nome'] ?? ''));
    $categoria_id = intval($_POST['categoria_id'] ?? 0);
    $quantidade_inicial = intval($_POST['quantidade_inicial'] ?? 0);
    $quantidade_minima = intval($_POST['quantidade_minima'] ?? 0);
    $custo = floatval($_POST['custo'] ?? 0);
    $valor_venda = floatval($_POST['valor_venda'] ?? 0);
    
    // Validar campos obrigatórios
    if (empty($nome) || $categoria_id <= 0 || $quantidade_inicial < 0 || $quantidade_minima < 0 || $custo <= 0 || $valor_venda <= 0) {
        $mensagem = 'Por favor, preencha todos os campos corretamente!';
        $classe_mensagem = 'alert-danger';
    } elseif ($valor_venda < $custo) {
        $mensagem = 'O valor de venda não pode ser menor que o custo!';
        $classe_mensagem = 'alert-danger';
    } else {
        // Verificar se a categoria existe
        $sql_verifica_categoria = "SELECT categoria_id FROM categorias WHERE categoria_id = $categoria_id";
        $resultado = $conn->query($sql_verifica_categoria);
        
        if ($resultado->num_rows === 0) {
            $mensagem = 'Categoria selecionada não existe!';
            $classe_mensagem = 'alert-danger';
        } else {
            // Atualizar o produto no banco de dados
            $sql_atualizar = "UPDATE produtos SET 
                              nome = '$nome', 
                              categoria_id = $categoria_id, 
                              quantidade_inicial = $quantidade_inicial, 
                              quantidade_minima = $quantidade_minima, 
                              custo = $custo, 
                              valor_venda = $valor_venda
                              WHERE id = $produto_id";
            
            if ($conn->query($sql_atualizar)) {
                $mensagem = 'Produto atualizado com sucesso!';
                $classe_mensagem = 'alert-success';
                
                // Atualizar os dados do produto após atualização bem-sucedida
                $sql_produto = "SELECT * FROM produtos WHERE id = $produto_id";
                $resultado = $conn->query($sql_produto);
                $produto = $resultado->fetch_assoc();
            } else {
                $mensagem = 'Erro ao atualizar produto: ' . $conn->error;
                $classe_mensagem = 'alert-danger';
            }
        }
    }
}

// Buscar categorias para o select
$categorias = array();
$sql_categorias = "SELECT categoria_id, nome FROM categorias ORDER BY nome";
$resultado = $conn->query($sql_categorias);

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $categorias[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GESTORMAX - Editar Produto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <?php include '../INCLUDE/sidebar.php'; ?>

      <!-- Main content -->
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">Editar Produto</h1>
        </div>

        <?php if (!empty($mensagem)): ?>
          <div class="alert <?php echo $classe_mensagem; ?> alert-dismissible fade show" role="alert">
            <?php echo $mensagem; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?php if ($produto): ?>
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-light">
            <h2 class="h5 card-title mb-1">Informações do Produto</h2>
            <p class="text-muted small mb-0">Atualize os dados do produto</p>
          </div>
          <div class="card-body">
            <form method="POST" action="">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="nome" class="form-label">Nome do Produto</label>
                  <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
                </div>
                
                <div class="col-md-6">
                  <label for="categoria_id" class="form-label">Categoria</label>
                  <select class="form-select" id="categoria_id" name="categoria_id" required>
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                      <option value="<?php echo $categoria['categoria_id']; ?>" <?php echo ($produto['categoria_id'] == $categoria['categoria_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categoria['nome']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                
                <div class="col-md-6">
                  <label for="quantidade_inicial" class="form-label">Quantidade Inicial</label>
                  <input type="number" class="form-control" id="quantidade_inicial" name="quantidade_inicial" min="0" value="<?php echo htmlspecialchars($produto['quantidade_inicial']); ?>" required>
                </div>
                
                <div class="col-md-6">
                  <label for="quantidade_minima" class="form-label">Estoque Mínimo</label>
                  <input type="number" class="form-control" id="quantidade_minima" name="quantidade_minima" min="1" value="<?php echo htmlspecialchars($produto['quantidade_minima']); ?>" required>
                  <div class="form-text">Quantidade mínima para alertas de estoque baixo</div>
                </div>
                
                <div class="col-md-6">
                  <label for="custo" class="form-label">Custo (R$)</label>
                  <input type="number" class="form-control" id="custo" name="custo" min="0" step="0.01" value="<?php echo htmlspecialchars($produto['custo']); ?>" required>
                  <div class="form-text">Valor pago pelo produto</div>
                </div>
                
                <div class="col-md-6">
                  <label for="valor_venda" class="form-label">Valor de Venda (R$)</label>
                  <input type="number" class="form-control" id="valor_venda" name="valor_venda" min="0" step="0.01" value="<?php echo htmlspecialchars($produto['valor_venda']); ?>" required>
                  <div class="form-text">Valor de venda ao cliente</div>
                </div>
              </div>
              
              <div id="margem-lucro" class="alert alert-info mt-3">
                <span>Margem de Lucro:</span>
                <span id="margem-lucro-valor" class="fw-bold">
                  <?php 
                    $margem = (($produto['valor_venda'] - $produto['custo']) / $produto['custo'] * 100);
                    echo number_format($margem, 2) . '%';
                  ?>
                </span>
              </div>
              
              <div class="d-flex justify-content-end mt-4 gap-2">
                <a href="produtos-lista.php" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Produto</button>
              </div>
            </form>
          </div>
        </div>
        <?php endif; ?>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Cálculo da margem de lucro em tempo real
    document.getElementById('custo').addEventListener('input', calcularMargem);
    document.getElementById('valor_venda').addEventListener('input', calcularMargem);
    
    function calcularMargem() {
      const custo = parseFloat(document.getElementById('custo').value) || 0;
      const venda = parseFloat(document.getElementById('valor_venda').value) || 0;
      
      if (custo > 0 && venda > 0) {
        const margem = ((venda - custo) / custo) * 100;
        document.getElementById('margem-lucro-valor').textContent = margem.toFixed(2) + '%';
      }
    }
  </script>
</body>
</html>
<?php
// Fechar conexão com o banco de dados
$conn->close();
?>
