<?php

// Inclui o arquivo de conexão
require_once 'conexao.php';

// Inicializa variáveis
$mensagem = '';
$classe_mensagem = '';

// Processa o formulário apenas se for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Valida e sanitiza o nome
        $nome = trim($_POST['nome'] ?? '');
        
        if (empty($nome)) {
            throw new Exception('O nome da categoria é obrigatório!');
        }

        // Prepara a query para verificar duplicatas
        $stmt_verifica = $conn->prepare("SELECT categoria_id FROM categorias WHERE nome = ?");
        $stmt_verifica->bind_param("s", $nome);
        $stmt_verifica->execute();
        $stmt_verifica->store_result();
        
        if ($stmt_verifica->num_rows > 0) {
            throw new Exception('Esta categoria já está cadastrada!');
        }

        // Prepara a query para inserção
        $stmt_insere = $conn->prepare("INSERT INTO categorias (nome) VALUES (?)");
        $stmt_insere->bind_param("s", $nome);
        
        if ($stmt_insere->execute()) {
            $mensagem = 'Categoria cadastrada com sucesso!';
            $classe_mensagem = 'alert-success';
            $_POST['nome'] = ''; // Limpa o campo após sucesso
        } else {
            throw new Exception('Erro ao cadastrar categoria: ' . $stmt_insere->error);
        }
        
    } catch (Exception $e) {
        $mensagem = $e->getMessage();
        $classe_mensagem = 'alert-danger';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Categoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../styles/sidebar.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../INCLUDE/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Cadastrar Nova Categoria</h1>
                </div>

                <?php if (!empty($mensagem)): ?>
                    <div class="alert <?= $classe_mensagem ?> alert-dismissible fade show" role="alert">
                        <?= $mensagem ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h2 class="h5 card-title mb-1">Informações da Categoria</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome da Categoria</label>
                                <input type="text" class="form-control" id="nome" name="nome" 
                                       value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Cadastrar</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Fecha a conexão
if (isset($conn)) {
    $conn->close();
}
?>
