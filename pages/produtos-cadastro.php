<?php
  include 'conexao.php';

// Processar o formulário quando enviado
$mensagem = '';
$classe_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            // Inserir o produto no banco de dados
            $sql_inserir = "INSERT INTO produtos (nome, categoria_id, quantidade_inicial, quantidade_minima, custo, valor_venda) 
                            VALUES ('$nome', $categoria_id, $quantidade_inicial, $quantidade_minima, $custo, $valor_venda)";
            
            if ($conn->query($sql_inserir) === TRUE) {
                $mensagem = 'Produto cadastrado com sucesso!';
                $classe_mensagem = 'alert-success';
                
                // Limpar os campos do formulário após cadastro bem-sucedido
                $_POST = array();
            } else {
                $mensagem = 'Erro ao cadastrar produto: ' . $conn->error;
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
  <title>GESTORMAX - Cadastrar Produto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
          <h1 class="h2">Cadastrar Novo Produto</h1>
        </div>

        <?php if (!empty($mensagem)): ?>
          <div class="alert <?php echo $classe_mensagem; ?> alert-dismissible fade show" role="alert">
            <?php echo $mensagem; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
          <div class="card-header bg-light">
            <h2 class="h5 card-title mb-1">Informações do Produto</h2>
            <p class="text-muted small mb-0">Preencha os dados do novo produto</p>
          </div>
          <div class="card-body">
            <form method="POST" action="">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="nome" class="form-label">Nome do Produto</label>
                  <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" required>
                </div>
                
                <div class="col-md-6">
                  <label for="categoria_id" class="form-label">Categoria</label>
                  <select class="form-select" id="categoria_id" name="categoria_id" required>
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                      <option value="<?php echo $categoria['categoria_id']; ?>" <?php echo (isset($_POST['categoria_id']) && $_POST['categoria_id'] == $categoria['categoria_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categoria['nome']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                
                <div class="col-md-6">
                  <label for="quantidade_inicial" class="form-label">Quantidade Inicial</label>
                  <input type="number" class="form-control" id="quantidade_inicial" name="quantidade_inicial" min="0" value="<?php echo htmlspecialchars($_POST['quantidade_inicial'] ?? '0'); ?>" required>
                </div>
                
                <div class="col-md-6">
                  <label for="quantidade_minima" class="form-label">Estoque Mínimo</label>
                  <input type="number" class="form-control" id="quantidade_minima" name="quantidade_minima" min="1" value="<?php echo htmlspecialchars($_POST['quantidade_minima'] ?? '10'); ?>" required>
                  <div class="form-text">Quantidade mínima para alertas de estoque baixo</div>
                </div>
                
                <div class="col-md-6">
                  <label for="custo" class="form-label">Custo (R$)</label>
                  <input type="number" class="form-control" id="custo" name="custo" min="0" step="0.01" value="<?php echo htmlspecialchars($_POST['custo'] ?? ''); ?>" required>
                  <div class="form-text">Valor pago pelo produto</div>
                </div>
                
                <div class="col-md-6">
                  <label for="valor_venda" class="form-label">Valor de Venda (R$)</label>
                  <input type="number" class="form-control" id="valor_venda" name="valor_venda" min="0" step="0.01" value="<?php echo htmlspecialchars($_POST['valor_venda'] ?? ''); ?>" required>
                  <div class="form-text">Valor de venda ao cliente</div>
                </div>
              </div>
              
              <div id="margem-lucro" class="alert alert-info mt-3 d-none">
                <span>Margem de Lucro:</span>
                <span id="margem-lucro-valor" class="fw-bold">0%</span>
              </div>
              
              <div class="d-flex justify-content-end mt-4 gap-2">
                <button type="button" id="btn-limpar" class="btn btn-outline-secondary">Limpar</button>
                <button type="submit" class="btn btn-primary">Cadastrar Produto</button>
              </div>
            </form>
          </div>
        </div>
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
        document.getElementById('margem-lucro').classList.remove('d-none');
      } else {
        document.getElementById('margem-lucro').classList.add('d-none');
      }
    }
    
    // Limpar formulário
    document.getElementById('btn-limpar').addEventListener('click', function() {
      document.querySelector('form').reset();
      document.getElementById('margem-lucro').classList.add('d-none');
    });
  </script>
</body>
</html>
<?php
// Fechar conexão com o banco de dados
$conn->close();
?>
