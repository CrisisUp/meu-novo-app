# Gestão CDI - Centro de Dia para Idosos 🏥👴👵

Este é um sistema web profissional desenvolvido com o ecossistema **Laravel** e **Tailwind CSS v4**, especializado no gerenciamento completo de Centros de Dia para Idosos (CDI). O sistema foca em acessibilidade, segurança de dados e eficiência operacional para equipes de assistência social e saúde.

---

## 🚀 Funcionalidades Principais

### 📋 Módulo de Idosos (Beneficiários)

* **Cadastro Visual:** Registro de dados pessoais com suporte a **Upload de Foto** para identificação rápida.
* **Identificação Oficial:** Suporte obrigatório a **NIS** e CPF, com máscaras de entrada dinâmicas.
* **Registro Único:** Geração automática de código de identificação único (Ex: `CDI-2026-0001`).
* **Prontuário Individual:** Ficha técnica detalhada com dados de saúde (alergias, medicamentos) e contatos de emergência.
* **Auditoria de Intercorrências:** Timeline de ocorrências com registro automático de qual profissional fez a anotação.

### 📅 Agenda de Atividades e Oficinas

* **Cronograma Semanal:** Gestão de oficinas (Fisioterapia, Música, Artesanato, etc) com definição de facilitador, dia e horário.
* **Gestão de Participantes:** Vínculo individual de idosos a atividades específicas para controle de engajamento.

### ✅ Controle de Frequência e Ponto

* **Chamada Diária:** Tabela inteligente para registro de presença/ausência em lote com anotações de intercorrência.
* **Ponto da Equipe:** Registro de entrada e saída dos funcionários diretamente no Dashboard, com contador de "Equipe no Posto" em tempo real.

### 🚑 Módulo de Encaminhamentos (Referência)

* **Fluxo de Saída:** Registro de idosos encaminhados para hospitais, UPAs, CRAS ou especialistas.
* **Classificação de Risco:** Níveis de prioridade (Urgente, Programado, Rotina) com histórico consolidado no prontuário.

### 📄 Documentação e Relatórios

* **Relatórios em PDF:** Geração de relatórios mensais de frequência formatados, com visualização prévia e escolha de período.
* **Exportação CSV:** Extração de dados da lista de idosos para Excel seguindo os filtros ativos.
* **Print-Friendly:** Layout do prontuário otimizado para impressão direta (`Ctrl+P`).

---

## 🎨 Design e UX (Padrão Enterprise)

* **Acessibilidade:** Tipografia de alto contraste e peso visual (Slate & Emerald).
* **Níveis de Acesso:** Diferenciação entre perfis `Administrador` e `Funcionário` para gestão da equipe profissional.
* **Navegação:** Breadcrumbs em todas as seções e filtros avançados na listagem.

---

## 🛣️ Estrutura de Endpoints (Rotas)

O sistema está organizado em módulos lógicos protegidos por autenticação:

### 👴 Gestão de Idosos

* `GET /idosos` : Listagem com filtros e miniaturas de fotos.
* `GET /idosos/{id}` : Prontuário eletrônico e timeline de saúde.
* `GET /idosos/exportar-csv` : BI e relatórios administrativos.

### 🗓️ Atividades e Oficinas

* `GET /atividades` : Cronograma geral de oficinas.
* `GET /atividades/{id}` : Gestão de participantes e detalhes da oficina.

### 📅 Operacional e Ponto

* `GET /frequencia` : Interface de chamada diária em lote.
* `POST /ponto/entrada` | `POST /ponto/saida` : Controle de jornada da equipe.

---

## 🛠️ Tecnologias Utilizadas

* **Backend:** Laravel 12 (PHP 8.4+)
* **Frontend:** Blade Templates & Alpine.js
* **Estilização:** Tailwind CSS v4
* **PDF:**Barryvdh Laravel-DomPDF
* **Storage:** Local Symlink (Imagens dos beneficiários)

---

## 💻 Como Iniciar o Projeto Localmente

1. **Clonar e Instalar:**

   ```bash
   git clone <url-do-repositorio>
   composer install && npm install
   ```

2. **Ambiente e Banco:**

   ```bash
   cp .env.example .env && php artisan key:generate
   touch database/database.sqlite && php artisan migrate
   php artisan storage:link
   ```

3. **Comandos Úteis:**
   * **Geração de Códigos Antigos:** `php artisan cdi:gerar-codigos`
   * **Promover Administrador:** `php artisan cdi:promote-admin {email}`
   * **Rodar o Servidor:** `php artisan serve` & `npm run dev`

---

&copy; 2026 — Gestão CDI. Sistema profissional para o cuidado e assistência à pessoa idosa.
