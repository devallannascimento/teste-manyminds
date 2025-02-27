<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Endereço</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

    <h2>Cadastrar Endereço</h2>

    <form action="<?= base_url('index.php/dashboard/adicionar_endereco/'.$user->id); ?>" method="post">
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuário</label>
            <input type="text" class="form-control" id="usuario" name="usuario_id" value="<?= $user->usuario; ?>" disabled>
        </div>

        <div class="mb-2">
            <label for="cep" class="form-label">CEP</label>
            <input type="text" name="cep" id="cep" class="form-control" placeholder="CEP" required>
        </div>

        <div class="mb-2">
            <label for="estado" class="form-label">Estado</label>
            <input type="text" name="estado" id="estado" class="form-control" placeholder="Estado" required readonly>
        </div>

        <div class="mb-2">
            <label for="cidade" class="form-label">Cidade</label>
            <input type="text" name="cidade" id="cidade" class="form-control" placeholder="Cidade" required readonly>
        </div>

        <div class="mb-2">
            <label for="bairro" class="form-label">Bairro</label>
            <input type="text" name="bairro" id="bairro" class="form-control" placeholder="Bairro" required readonly>
        </div>

        <div class="mb-2">
            <label for="rua" class="form-label">Rua</label>
            <input type="text" name="rua" id="rua" class="form-control" placeholder="Rua" required readonly>
        </div>

        <div class="mb-2">
            <label for="numero" class="form-label">Número</label>
            <input type="text" name="numero" id="numero" class="form-control" placeholder="Número" required>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Salvar
        </button>
            <a href="<?= site_url('dashboard'); ?>" class="btn btn-outline-primary">Voltar</a>
    </form>
    
    <script>
        // Ao sair do campo CEP, chama a API do ViaCEP
        document.getElementById('cep').addEventListener('blur', function() {
            var cep = this.value.replace(/\D/g, ''); // Remove tudo o que não for número

            if (cep !== "") {
                var validacep = /^[0-9]{8}$/; // Verifica se o CEP é válido

                if (validacep.test(cep)) {
                    // Exibe "..." enquanto está buscando
                    document.getElementById('estado').value = "...";
                    document.getElementById('cidade').value = "...";
                    document.getElementById('bairro').value = "...";
                    document.getElementById('rua').value = "...";

                    // Faz a requisição para a API ViaCEP
                    var script = document.createElement('script');
                    script.src = 'https://viacep.com.br/ws/' + cep + '/json/?callback=preencherEndereco';
                    document.body.appendChild(script);
                } else {
                    alert("CEP inválido!");
                }
            }
        });

        // Função que será chamada quando a resposta da API for recebida
        function preencherEndereco(conteudo) {
            if (!("erro" in conteudo)) {
                // Preenche os campos com os dados do endereço retornados
                document.getElementById('estado').value = conteudo.uf;
                document.getElementById('cidade').value = conteudo.localidade;
                document.getElementById('bairro').value = conteudo.bairro;
                document.getElementById('rua').value = conteudo.logradouro;
            } else {
                alert("CEP não encontrado.");
            }
        }
    </script>

</body>
</html>
