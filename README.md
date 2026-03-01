# 🗓️ AgendX — Sistema de Agendamento para Clínicas

Sistema web desenvolvido em **Laravel** para gerenciamento de agendamentos em clínicas e consultórios.

O AgendX permite que administradores gerenciem profissionais, disponibilidades e atendimentos, enquanto clientes podem realizar agendamentos de forma pública e simples.

---

## 🚀 Funcionalidades

### 🔐 Área Administrativa
- Dashboard com métricas
- Cadastro de profissionais
- Cadastro de disponibilidades por data e horário
- Gestão de clientes
- Controle de agendamentos
- Atualização de status do atendimento

### 🌐 Área Pública
- Consulta de horários disponíveis
- Agendamento por clientes
- Busca de agendamentos por documento
- Fluxo simplificado para marcação

---

## 🧠 Regras de Negócio

- Cada profissional possui disponibilidades por **data específica**
- Os horários são gerados com base em:
  - `start_time`
  - `end_time`
  - `duration_minutes`
- Um horário não pode ser agendado duas vezes (unicidade garantida)
- Agendamentos possuem status:
  - `scheduled`
  - `completed`

---

## 🏗️ Tecnologias Utilizadas

- PHP 8+
- Laravel 12
- MySQL
- TailwindCSS
- Blade

---


## 🔄 Rotas Principais

### 🔐 Admin (auth)

- `/dashboard`
- `/professionals`
- `/availabilities`
- `/appointments`
- `/clients`

---

### 🌐 Público

Prefixo: `/agendamento`

- `GET /agendamento`
- `POST /agendamento/store`
- `GET /agendamento/by-document`
- `GET /agendamento/available-slots`

---

## ⚙️ Instalação

```bash
# Clonar repositório
git clone https://github.com/seu-usuario/agendx.git

cd agendx

# Instalar dependências
composer install
npm install

# Copiar .env
cp .env.example .env

# Gerar chave
php artisan key:generate

# Configurar banco no .env

# Rodar migrations
php artisan migrate

# Rodar seeder do usuário
php artisan db:seed --class UserSeeder

# Rodar servidor
php artisan serve