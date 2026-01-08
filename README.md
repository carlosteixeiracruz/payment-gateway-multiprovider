Payment Gateway Multiprovider

Este projeto é uma API de integração de pagamentos desenvolvida em Laravel 11. O sistema utiliza o padrão de projeto Adapter para permitir a integração com múltiplos provedores de pagamento (como Stripe e PayPal) de forma flexível e escalável.
Descrição do Projeto

A aplicação gerencia o fluxo completo de uma transação financeira, desde a autenticação do usuário e a criação da intenção de compra até o processamento de confirmações automáticas via Webhooks.
O que foi implementado:

    Arquitetura de Pagamentos: Implementação de um Service Provider e um PaymentProcessor que isola a lógica de cada operadora de pagamento.

    Autenticação via API: Uso do Laravel Sanctum para proteção de rotas, garantindo que apenas usuários autenticados possam realizar compras e consultar históricos.

    Segurança e Validação: * Criação de FormRequests para validar dados de entrada (bloqueando valores negativos ou provedores não suportados).

        Tratamento global de exceções para ocultar erros técnicos do banco de dados e exibir mensagens amigáveis (JSON) ao cliente.

    Sistema de Webhooks: Rota pública preparada para receber notificações assíncronas dos provedores, atualizando o status das transações em tempo real.

    Idempotência: Controle via cache para evitar o processamento duplicado de notificações de pagamento (Webhooks) enviadas pelo mesmo evento.

Tecnologias Utilizadas

    Linguagem: PHP 8.2+

    Framework: Laravel 11

    Banco de Dados: MySQL

    Ambiente: Docker (Laravel Sail)

    Autenticação: Laravel Sanctum

Requisitos para Instalação

Para rodar o projeto localmente, você precisará do Docker instalado.

    Clone o repositório: git clone https://github.com/carlosteixeiracruz/payment-gateway-multiprovider.git

    Acesse a pasta do projeto: cd payment-gateway-multiprovider

    Suba o ambiente Docker: ./vendor/bin/sail up -d

    Instale as dependências do Composer: ./vendor/bin/sail composer install

    Gere a chave da aplicação e rode as migrations: ./vendor/bin/sail php artisan key:generate ./vendor/bin/sail php artisan migrate

Documentação da API
Rotas de Autenticação

    POST /api/register: Cria um novo usuário (campos: name, email, password, password_confirmation).

    POST /api/login: Autentica o usuário e retorna o Token Bearer.

Rotas de Pagamento (Requerem Token)

    POST /api/purchase: Inicia um processo de pagamento.

        Campos: provider (stripe ou paypal), amount (numérico, min 1), currency (ex: BRL).

    GET /api/transactions: Retorna a lista de todas as transações do usuário logado.

Rotas de Integração (Públicas)

    POST /api/webhook/{provider}: Recebe o status da transação vindo do provedor externo.

        Campos: event_id, transaction_id, status.

Estrutura do Banco de Dados

A tabela principal 'transactions' contém os seguintes campos:

    user_id: Relacionamento com o usuário.

    amount: Valor da transação.

    currency: Moeda utilizada.

    provider: Nome do provedor (Stripe/PayPal).

    provider_id: ID de referência gerado pelo provedor externo.

    status: Estado atual (pending, paid, success, failed).

Desenvolvido por Carlos Teixeira Cruz.