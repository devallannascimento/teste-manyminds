<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação do WebService</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        pre {
            background-color: #dbdbdb;
            border-left: 5px solid #007bff;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        code {
            background-color: #dbdbdb;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 14px;
            color:blue;
        }
    </style>
</head>
<body class="container mt-4">

    <h2>Documentação do WebService</h2>

    <p>Este WebService fornece acesso a registros do sistema e é protegido por autenticação via API Key. Apenas usuários com permissão adequada podem gerar e utilizar a API Key.</p>

    <hr>

    <h4>1. Utilização</h4>

    <p>No fim da página da documentação você encontrará um botão para gerar a sua própria API Key.</p>
    <p></p>

    <h5>Recuperando Logs de Registro</h5>
    <p><strong>Endpoint:</strong></p>
    <pre> GET /api/logs</pre>

    <p><strong>Autenticação:</strong><br>A requisição deve conter a API Key no cabeçalho <code>Authorization</code>.</p>

    <p><strong>Headers:</strong></p>
    <pre>
{
  "Authorization": "sua-api-key-aqui"
}</pre>

    <p><strong>Resposta de Sucesso:</strong></p>
    <pre>
{
  "status": "success",
  "logs": [
    {
      "id": 1,
      "usuario": "admin",
      "acao": "Login realizado",
      "tabela": "auth_logs",
      "data": "2025-02-26 10:30:00"
    },
    {
      "id": 2,
      "usuario": "master",
      "acao": "Gerou uma API Key",
      "tabela": "api_keys",
      "data": "2025-02-26 11:00:00"
    }
  ]
}</pre>

    <p><strong>Resposta de Erro (API Key inválida ou ausente):</strong></p>
    <pre>
{
  "status": "error",
  "message": "API Key inválida"
}</pre>

    <hr>

    <h4>2. Como Testar via Postman</h4>
    <h5>Consultar Logs:</h5>
    <ol>
        <li>Criar uma nova requisição <code>GET</code> para <code>http://localhost/teste-manyminds/index.php/api/logs</code>.</li>
        <li>Adicionar o cabeçalho <code>Authorization</code> com a API Key gerada.</li>
        <li>Enviar a requisição e verificar os logs retornados.</li>
    </ol>

    <hr>

    <h4>3. Segurança</h4>
    <ul>
        <li>As API Keys são geradas de forma única para cada usuário.</li>
        <li>A API Key deve ser armazenada com segurança e não compartilhada.</li>
        <li>Todas as ações são registradas no log do sistema para auditoria.</li>
    </ul>

    <hr>

    <h4>4. Considerações Finais</h4>
    <p>Este WebService proporciona um acesso seguro aos registros do sistema, garantindo que apenas usuários autorizados possam consultá-los. Para aprimoramentos futuros, podem ser implementadas funcionalidades como expiração da API Key, controle de permissões mais refinado, novas funções e outras medidas de segurança avançadas.</p>

    <hr>

    <div id="resultadoApiKey" class="mt-3"></div>

    <div style="margin-bottom: 40px;">
        <button id="gerarApiKey" class="btn btn-primary">Gerar API Key</button>
        <a href="<?= site_url('dashboard'); ?>" class="btn btn-outline-primary">Voltar</a>
    </div>


    <script>
        document.getElementById('gerarApiKey').addEventListener('click', function() {
            
            fetch('<?= base_url('index.php/api/gerar_chave'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('resultadoApiKey').innerHTML = 
                        '<div class="alert alert-success">Sua chave de API gerada: ' + data.api_key + '</div>';
                } else {
                    document.getElementById('resultadoApiKey').innerHTML = 
                        '<div class="alert alert-danger">Erro: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                document.getElementById('resultadoApiKey').innerHTML = 
                    '<div class="alert alert-danger">Erro ao gerar chave de API.</div>';
            });
        });
    </script>

</body>
</html>
