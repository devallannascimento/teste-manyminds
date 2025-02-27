<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-left flex-grow-1">Dashboard</h2>
        <div>
            
            <?php 
            $user_id = $this->session->userdata('user_id'); 
            $usuario = $this->Usuario_model->get_user_data($user_id);
            
            if ($usuario && $usuario->role == 'master' || $usuario && $usuario->role == 'admin'): ?>
                <a href="<?= site_url('logs'); ?>" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-list-alt"></i> Logs
                </a>
                <a href="<?= site_url('api'); ?>" class="btn btn-outline-primary me-2">
                        <i class="fas fa-book"></i> API
                    </a> 
                <?php if ($usuario && $usuario->role == 'master'): ?>
                    <a href="<?= site_url('auth/cadastro_admin'); ?>" class="btn btn-outline-success me-2">
                        <i class="fas fa-plus"></i> Cadastrar Administrador
                    </a> 
                <?php endif; ?>
            <?php endif; ?>
            <a href="<?= site_url('auth/logout'); ?>" class="btn btn-outline-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Email</th>
                <th>Status</th>
                <th">Ações</th>
                <th>Endereços</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $user): ?>
                <tr>
                    <td><?= $user->id; ?></td>
                    <td><?= $user->usuario; ?></td>
                    <td><?= $user->email; ?></td>
                    <td><?= $user->ativo ? 'Ativo' : 'Inativo'; ?></td>
                    <td>
                        <?php if ($user->ativo): ?>
                            <a href="<?= site_url('dashboard/inativar/'.$user->id); ?>" class="btn btn-warning">
                                <i class="fas fa-ban"></i> Inativar
                            </a>
                        <?php else: ?>
                            <a href="<?= site_url('dashboard/reativar/'.$user->id); ?>" class="btn btn-success">
                                <i class="fas fa-check"></i> Reativar
                            </a>
                        <?php endif; ?>

                        <?php if ($user->ativo): ?>
                            <a href="<?= site_url('dashboard/editar_usuario/'.$user->id); ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        <?php else: ?>
                            <button class="btn btn-primary" disabled>
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        <?php endif; ?>

                        <?php if ($user->ativo): ?>
                            <a href="<?= site_url('dashboard/cadastro_endereco/'.$user->id); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Endereço
                            </a>
                        <?php else: ?>
                            <button class="btn btn-primary" disabled>
                                <i class="fas fa-plus"></i> Endereço
                            </button>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div id="enderecos_<?= $user->id; ?>">
                            <ul>
                                <?php 
                                $enderecos = $this->Endereco_model->get_by_usuario($user->id);
                                if (!empty($enderecos)): 
                                    foreach ($enderecos as $endereco): ?>
                                        <li style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                            <span style="max-width: 85%; word-wrap: break-word;">
                                                <?= $endereco->rua; ?>, <?= $endereco->bairro; ?>, <?= $endereco->cidade; ?>, <?= $endereco->estado; ?> - <?= $endereco->cep; ?>
                                            </span>
                                            <div>
                                                <?php if ($user->ativo): ?>
                                                    <a href="<?= site_url('dashboard/editar_endereco/'.$endereco->id); ?>" 
                                                       class="btn btn-primary btn-sm"
                                                       style="padding: 4px 6.5px;" 
                                                       title="Editar Endereço">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= site_url('dashboard/excluir_endereco/'.$endereco->id); ?>" 
                                                       class="btn btn-danger btn-sm" 
                                                       onclick="return confirm('Tem certeza que deseja excluir este endereço?')"
                                                       title="Excluir Endereço">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-warning btn-sm" disabled>
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm" disabled>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; 
                                else: ?>
                                    <li class="text-muted">
                                        Nenhum endereço cadastrado.
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
