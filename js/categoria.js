
document.addEventListener("DOMContentLoaded", function () {
    function limparCampo() {
        document.getElementById("nome").value = "";
    }

    function cadastrarCategoria(event) {
        if (event) event.preventDefault();
        const modal = document.getElementById("modalSucesso");

        modal.textContent = "Categoria cadastrada com sucesso!";
        modal.classList.add("ativo");

        setTimeout(() => {
            modal.classList.remove("ativo");
            modal.textContent = "";
        }, 3000);

        document.getElementById("nome").value = "";
    }

    // Disponibilizar funções no escopo global, se necessário
    window.limparCampo = limparCampo;
    window.cadastrarCategoria = cadastrarCategoria;
});

