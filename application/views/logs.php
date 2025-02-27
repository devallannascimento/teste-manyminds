<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Logs do Sistema</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-left flex-grow-1">Logs do Sistema</h2>
        <div>
            <a href="<?= site_url('dashboard'); ?>" class="btn btn-outline-secondary me-2">
               Dashboard
            </a>
            <a href="<?= site_url('auth/logout'); ?>" class="btn btn-outline-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th style="width: 190px;">Ação</th>
                <th style="width: 145px;">Tabelas Afetadas</th>
                <th style="width: 100.9px;">ID Registro</th>
                <th>Detalhes</th>
                <th style="width: 162px;">Data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= $log->id; ?></td>
                    <td><?= $log->usuario; ?></td>
                    <td><?= $log->acao; ?></td>
                    <td><?= $log->tabela_afetada; ?></td>
                    <td><?= $log->registro_id; ?></td>
                    <td><?= $log->detalhes; ?></td>
                    <td><?= $log->data; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
