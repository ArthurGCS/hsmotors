# Configuração do Supabase para HS Motors

Este documento explica como configurar o Supabase para o sistema HS Motors.

## 1. Criar uma conta no Supabase

1. Acesse [https://supabase.com/](https://supabase.com/) e crie uma conta
2. Crie um novo projeto

## 2. Configurar as tabelas

1. No painel do Supabase, vá para a seção "SQL Editor"
2. Crie uma nova consulta
3. Cole o conteúdo do arquivo `supabase_tables.sql`
4. Execute a consulta para criar as tabelas e inserir dados de exemplo

## 3. Configurar o armazenamento para imagens

1. No painel do Supabase, vá para a seção "Storage"
2. Crie um novo bucket chamado "veiculos"
3. Configure as permissões para permitir upload e download de imagens

## 4. Configurar a autenticação (opcional)

1. No painel do Supabase, vá para a seção "Authentication"
2. Configure os métodos de autenticação desejados (email, Google, etc.)

## 5. Obter as credenciais

1. No painel do Supabase, vá para a seção "Settings" > "API"
2. Copie a URL do projeto e a chave anônima
3. Atualize o arquivo `config/supabase.php` com essas informações

## 6. Configurar a string de conexão

1. No painel do Supabase, vá para a seção "Settings" > "Database"
2. Copie a string de conexão PostgreSQL
3. Atualize o arquivo `config/supabase.php` com essa string

## 7. Testar a conexão

1. Acesse o sistema HS Motors
2. Verifique se os dados de exemplo estão sendo exibidos corretamente

