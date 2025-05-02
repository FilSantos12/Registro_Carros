# - Sistema de Cadastro de Veículos 
##📌 Descrição
O Sistema de Cadastro de Veículos foi desenvolvido para atender uma nescessiadade especifica de uma empresa de depachante 
localizada na cidade de Fortaleza-CE (Paulista Despachante).
Foi Solicitado um sistema WEB pode ser acesso de qualquer lugar por meio de diferentes dispositvos.
Com este sistema, é possível cadastrar veículos individualmente ou em massa através de upload de documentos PDF, 
extraindo automaticamente informações como placa e tipo de documento para preenchimento do cadastro.

## 🚀 Funcionalidades Principais
- Cadastro Individual
- Registro completo de veículos com placa, Renavam, CRV e código de segurança
- Upload de documentos PDF associados a cada veículo
- Sistema de observações para acompanhamento
- Upload em Massa
- Processamento automatizado de múltiplos arquivos PDF simultaneamente (até 50 arquivos)
- Reconhecimento automático da placa pelo nome do arquivo (formato: PLACA_TIPODOC.pdf)
- Validação inteligente de formatos de placa (antigo e Mercosul)

# Gestão de Documentos
Armazenamento seguro de arquivos PDF em diretório específico
Validação de tipo (apenas PDF) e tamanho (até 10MB por arquivo)
Geração automática de nomes únicos para os documentos

## 🛠️ Tecnologias Utilizadas
- ##Frontend
- ##HTML5 - Estrutura da aplicação
- ##CSS3 - Estilização com Bootstrap 5.3
- ##JavaScript - Validações e interações
- ##Bootstrap Icons - Ícones intuitivos

## Backend
- ##PHP 8.2 - Lógica de negócios
- ##MySQL - Armazenamento de dados
- ##Prepared Statements - Segurança contra SQL injection

## Ferramentas
- ##Visual Studio Code - Ambiente de desenvolvimento
- ##Git - Controle de versão
- ##XAMPP - Ambiente de desenvolvimento local

📋 Pré-requisitos
- ##Servidor web (Apache, Nginx)
- ##PHP 8.0 ou superior
- ##MySQL 5.7 ou superior
- ##Extensão PHP para MySQL habilitada
- ##Permissão de escrita na pasta /uploads

🚦 Status do Projeto
✅ Versão 2.0.3 - Concluido Validado e funcionando.
