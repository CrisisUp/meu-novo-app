# Guia de Instalação: Gestão CDI no Windows 🪟

Este guia detalha como instalar e rodar o **Sistema de Gestão CDI** localmente no computador da instituição utilizando o ambiente **Laragon**.

---

## 🛠️ Requisitos Iniciais

Antes de começar, certifique-se de ter instalado:

1. **Laragon Full:** [Baixar aqui](https://laragon.org/download/) (Recomendado para PHP 8.2+)
2. **Git para Windows:** [Baixar aqui](https://git-scm.com/download/win)

---

## 🚀 Passo a Passo da Instalação

### 1. Preparar a Pasta do Projeto

1. Abra o Laragon.
2. Clique no ícone de pasta ou navegue até `C:\laragon\www`.
3. Cole a pasta do sistema nesta localização. (Ex: `C:\laragon\www\gestao-cdi`).

### 2. Configurar o Ambiente (Terminal)

No painel do Laragon, clique no botão **"Terminal"** e digite os seguintes comandos:

## Entrar na pasta do projeto

```bash
cd gestao-cdi
```

## Instalar dependências do PHP

```bash
composer install
```

## Instalar dependências de Estilização

```bash
npm install
```

## Gerar a chave de segurança

```bash
php artisan key:generate
```

## Criar o arquivo de banco de dados (se não existir)

```bash
copy .env.example .env
```

## (Lembre-se de verificar se DB_CONNECTION está como sqlite no .env)

### 3. Banco de Dados e Assets

Ainda no terminal do Laragon:

## Criar as tabelas no banco de dados

```bash
php artisan migrate
```

## Compilar os arquivos visuais para produção

```bash
npm run build
```

## Criar o link para as fotos dos idosos aparecerem

```bash
    php artisan storage:link
```

### 4. Acessar o Sistema

1. No Laragon, clique em **"Stop"** e depois em **"Start All"**.
2. O Laragon detectará o projeto e você poderá acessar pelo navegador no endereço:
   👉 **<http://gestao-cdi.test>**

   ---

   ## 🐳 Opção 2: Instalação via Docker (Recomendado)

   Se você utiliza **Docker Desktop**, a instalação é ainda mais rápida e isolada.

   ### 1. Subir os Containers

   Na pasta do projeto, abra o terminal e rode:

   ```bash
   docker-compose up -d --build
   ```

   ### 2. Configurar o Sistema (Apenas na primeira vez)

   Rode os comandos dentro do container para preparar o banco e as pastas:

   ```bash
   docker exec -it gestao-cdi-app composer install
   docker exec -it gestao-cdi-app php artisan key:generate
   docker exec -it gestao-cdi-app php artisan migrate
   docker exec -it gestao-cdi-app php artisan storage:link
   ```

   ### 3. Acessar

   O sistema estará disponível em:
   👉 **<http://localhost:8000>**

   ---

   ## 🛡️ Criando o Administrador Inicial

Para conseguir gerenciar a equipe, você precisa promover o primeiro usuário cadastrado para Administrador. No terminal do Laragon, rode:

```bash
php artisan cdi:promote-admin seu-email@exemplo.com
```

---

## 💾 Rotina de Backup (IMPORTANTE)

Como o sistema armazena dados sensíveis, siga esta rotina:

1. Vá até a pasta `C:\laragon\www\gestao-cdi\database`.
2. Copie o arquivo **`database.sqlite`**.
3. Guarde uma cópia em um **Pendrive** ou **Nuvem** (Google Drive/OneDrive) toda sexta-feira.
4. Caso precise trocar de computador, basta instalar o Laragon no novo e colar este arquivo de volta na mesma pasta.

---

## 🔧 Solução de Problemas Comuns

* **Erro 404:** Certifique-se de que o Laragon está rodando e que você digitou o endereço `.test` corretamente.
* **Fotos não aparecem:** Verifique se você rodou o comando `php artisan storage:link`.
* **Hora errada:** Verifique se o `APP_TIMEZONE=America/Sao_Paulo` está correto no arquivo `.env`.

---

## 📸 Configurações de Upload de Fotos (IMPORTANTE)

O sistema está configurado para aceitar fotos de até **10MB** no nível da aplicação (Laravel). No entanto, para que o servidor aceite esses arquivos (especialmente fotos de alta resolução de celulares modernos), você **deve** ajustar as configurações do seu PHP.

### Como ajustar no Laragon ou Herd

1. Localize o arquivo de configuração do PHP (`php.ini`).
2. Procure as seguintes linhas e altere os valores para o recomendado abaixo:

```ini
; Tamanho máximo de um único arquivo de upload
upload_max_filesize = 2MB

; Tamanho máximo de todos os dados enviados via POST (deve ser maior ou igual ao de cima)
post_max_size = 2MB

; Limite de memória do PHP para processamento de imagens (recomendado)
memory_limit = 256M
```

1. **Reinicie o servidor** (Laragon ou Herd) para que as mudanças entrem em vigor.

> **Nota:** Se você não fizer esse ajuste, o sistema retornará o erro *"413 Content Too Large"* ou a foto simplesmente não será salva se exceder o limite padrão de 2MB do PHP.

---
&copy; 2026 — Documentação de Implantação Local Gestão CDI.
