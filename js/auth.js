<script>
  document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const usuario = document.getElementById('username').value.trim();
    const senha = document.getElementById('password').value.trim();
    const errorDiv = document.getElementById('login-error');

    const usuarios = {
      'admin': 'admin',
      'teste': 'teste123'
    };

    if (usuarios.hasOwnProperty(usuario)) {
      if (usuarios[usuario] === senha) {
        alert('Login bem-sucedido! Redirecionando...');
        window.location.href = 'dashboard.php';
      } else {
        errorDiv.textContent = 'Senha incorreta!';
        errorDiv.classList.remove('d-none');
      }
    } else {
      errorDiv.textContent = 'Usuário não encontrado!';
      errorDiv.classList.remove('d-none');
    }
  });
</script>