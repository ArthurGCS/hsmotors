# HS Motors - Sistema de Gerenciamento de Veículos

Este é um sistema completo para gerenciamento de veículos e clientes para a concessionária HS Motors.

## Requisitos

- PHP 7.4 ou superior
- MySQL ou PostgreSQL (via Supabase)
- Servidor web (Apache, Nginx, etc.)

## Configuração

### Modo de Teste (MySQL)

1. Crie um banco de dados MySQL chamado `hs_motors`
2. Configure as credenciais no arquivo `config/database.php` se necessário
3. Acesse o sistema pelo navegador e os dados de exemplo serão criados automaticamente

### Modo de Produção (Supabase)

1. Abra o arquivo `config/database.php`
2. Altere a variável `$test_mode` para `false`
3. Abra o arquivo `config/supabase.php`
4. Substitua a chave anônima pelo seu valor real do Supabase
5. Substitua a senha na string de conexão pela sua senha real do Supabase

## Estrutura do Projeto

- `/admin` - Área administrativa
- `/assets` - Arquivos CSS, JavaScript e imagens
- `/config` - Configurações do banco de dados
- `/includes` - Arquivos de inclusão (funções, cabeçalhos, rodapés)
- `/uploads` - Diretório para upload de imagens

## Funcionalidades

- Gerenciamento de clientes
- Gerenciamento de veículos
- Vinculação de clientes a veículos
- Site público para exibição de veículos
- Formulário de contato e interesse

## Créditos

Desenvolvido para HS Motors.

