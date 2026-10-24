# Instalação e transporte para o laboratório

Para atualizar uma instalação existente com 25 jogos, use ATUALIZAR.md. A instalação nova usa instalar.sql e inclui 50 jogos.

## Pasta

O caminho correto é C:\xampp\htdocs\gamestock_final\index.php. Se a extração criou uma pasta dentro de outra, mova a pasta que contém index.php para htdocs. VS Code serve para ler e alterar código; quem executa o PHP é o Apache/PHP do XAMPP.

## Portas

Use a porta Apache exibida no painel para abrir o navegador: 80 permite localhost; 8080 exige localhost:8080. No banco, confira a porta do MySQL: config/database.php usa 3307; config/local.example.php usa 3306. Copie o exemplo para local.php e ajuste o número se necessário. Não altere o arquivo de configuração do MySQL do laboratório se ele já estiver verde.

Se o phpMyAdmin já abre e importa SQL, sua conexão com o banco está funcionando. A conexão do projeto ainda precisa usar a mesma porta e as credenciais locais. Se a senha root não for vazia, informe-a em config/local.php. O nome do banco deve continuar gamestock_final.

## Mensagens comuns

- Não foi possível acessar o banco: confira MySQL iniciado, porta, usuário/senha e importação do SQL.
- Unknown database: importe database/instalar.sql.
- Table already exists: o banco já foi instalado. Não importe novamente sobre as mesmas tabelas.
- Página não encontrada: confira pasta, index.php e porta Apache.
- Could not find driver: habilite pdo_mysql no PHP do XAMPP e reinicie o Apache.
- Login não funciona: use as novas contas deste pacote. Pedro usa Pedro123!, diferente da senha do projeto anterior.
- Jogo não pode ser excluído: ele está ligado ao histórico de um pedido. Desative-o ou use um jogo criado apenas para demonstrar exclusão.

## Levar uma instalação nova

Leve o ZIP completo no pendrive, por e-mail ou outro meio. Extraia na faculdade, copie a pasta para htdocs, inicie XAMPP, importe instalar.sql e ajuste config/local.php para aquele computador. A apresentação básica funciona sem internet; os links das lojas externas precisam de internet.

## Levar seus dados alterados

O código e o banco são separados. Antes de sair de casa, exporte **gamestock_final** no phpMyAdmin para um SQL novo, além de copiar toda a pasta do projeto. Na faculdade importe esse backup em uma base compatível vazia, em vez do instalador inicial. O instalar.sql contém os dados iniciais, não suas alterações de uso.

Antes de substituir uma base existente, confirme que ela pertence ao seu projeto e exporte um backup. Não exclua outras bases. Mantenha config/local.php específico de cada máquina. Não inclua backups com dados reais no repositório público.
