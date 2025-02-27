<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Endereço</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

    <h2>Editar Endereço</h2>
    
    <form action="<?= site_url('dashboard/atualizar_endereco/'.$endereco->id); ?>" method="post">
        <div class="mb-2">
            <input type="text" name="cep" class="form-control" placeholder="CEP" value="<?= $endereco->cep ?>" required>
        </div>
        <div class="mb-2">
            <input type="text" name="estado" class="form-control" placeholder="Estado" value="<?= $endereco->estado ?>" required>
        </div>
        <div class="mb-2">
            <input type="text" name="cidade" class="form-control" placeholder="Cidade" value="<?= $endereco->cidade ?>" required>
        </div>
        <div class="mb-2">
            <input type="text" name="bairro" class="form-control" placeholder="Bairro" value="<?= $endereco->bairro ?>" required>
        </div>
        <div class="mb-2">
            <input type="text" name="rua" class="form-control" placeholder="Rua" value="<?= $endereco->rua ?>" required>
        </div>
        
        <button type="submit" class="btn btn-primary">
            Atualizar Endereço
        </button>
    </form>

</body>
</html>
