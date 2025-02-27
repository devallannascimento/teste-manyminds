# Projeto - Teste Técnico Manyminds

Este projeto foi desenvolvido como parte de um teste técnico para a empresa **Manyminds**. Ele utiliza o **PHP 7.4**, **Apache**, **CodeIgniter 3** e **MariaDB** para implementar um sistema completo de autenticação, controle de usuários e um **WebService seguro**.

## Funcionalidades

- **Autenticação**: Login e logout seguros.
- **CRUD de Usuários e Endereços**:
  - Cadastro, edição e inativação de usuários (exclusão não permitida, apenas desativação).
  - Usuários podem ser reativados posteriormente.
  - No caso dos endereços, é possível realizar a exclusão definitiva.
  - Integração com a API **ViaCEP** para facilitar o cadastro de endereços.
- **Gerenciamento de Admins e Usuários**:
  - Existe um **usuário master** criado por padrão, que conta com todas as permissões, além de poder cadastrar novos administradores e novos usuários master.
  - Usuários admin podem gerenciar logs e usuários comuns.
- **Controle de Acessos**:
  - O sistema implementa um sistema de **roles**: `master`, `admin` e `usuario`.
  - Algumas rotas são restritas apenas ao **master** e ao **admin**.
- **Sistema de Logs**:
  - Todos os logs são armazenados e acessíveis via **WebService protegido por ApiKey**.
  - A dashboard permite ao usuários `master` e `admin` visualizar logs e usuários, além de gerar uma ApiKey própria.
- **Segurança**:
  - As senhas dos usuários são armazenadas de forma segura utilizando um algoritmo de hashing moderno, garantindo maior proteção contra ataques de força bruta e vazamentos de dados.

## Instalação e Configuração
1. Clone o repositório:
   ```sh
   git clone <repo-url>
   cd teste-manyminds
   ```
2. Configure o banco de dados no arquivo `application/config/database.php`.

   Por padrão ele usa uma database chamada manyminds, que pode ser criada usando o comando no seu banco de dados:
   ```
   CREATE DATABASE manyminds CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Verifique a conexão do projeto com banco de dados.
   
   Acesse `localhost/teste-manyminds/` no seu navegador e deve exibir a tela de login. Caso se depare com a mensagem `Access denied for user 'root'@'localhost'` pode significar que a sua senha esteja incorreta no `database.php`.
   <br><br>Utilize o comando abaixo para criar a sua senha no banco e insira a senha correta no `database.php`.
   ```
   sudo mysql_secure_installation
   ```
4. Crie a estruta de tabelas.

   Execute no seu terminal comando:
   ```
   curl http://localhost/teste-manyminds/index.php/CreateDatabase
   ```
   Você deve receber a seguinte saída:
   ```
   Conexão com a datebase foi bem-sucedida!
   Tabela usuarios criada com sucesso!
   Tabela de tentativas de login criada com sucesso!
   Tabela de endereços criada com sucesso!
   Tabela de logs criada com sucesso!
   Tabela de api_keys criada com sucesso!
   Usuário master já existe!
   ```
   Após isso o seu sistema já estará funcionando. Por padrão ao executar esse comando é criado um usuário `master` com a senha `admin123`.

## WebService (API)
Um webservice de retorno de registros do sistema. A documentação completa do WebService está disponível em:
```
localhost/teste-manyminds/index.php/api
```
Ela inclui todas as informações necessárias sobre autenticação, endpoints disponíveis e formato das requisições e respostas.

