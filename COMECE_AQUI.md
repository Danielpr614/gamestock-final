# Primeiro acesso — Daniel Pena

**Já instalou a versão com 25 jogos? Siga ATUALIZAR.md em vez de reinstalar o banco.**

1. Copie **gamestock_final** para **C:\xampp\htdocs**. Não substitua sua pasta antiga loja_games.
2. Abra XAMPP e clique em Start para Apache e MySQL.
3. Abra o phpMyAdmin, clique em Importar e escolha **database/instalar.sql** desta pasta. Execute uma única vez no banco novo.
4. Se o MySQL do painel usa 3306, copie **config/local.example.php** para **config/local.php**. O exemplo já usa 3306. Se usa 3307, a configuração padrão já atende.
5. Abra **http://localhost/gamestock_final/**. Se Apache usa 8080, abra **http://localhost:8080/gamestock_final/**.
6. Clique em Entrar: **evandro@gmail.com** / **admin123**.

Cliente para testar carrinho: **pedro@gmail.com** / **Pedro123!**.

Se houver erro, pare nessa etapa e envie a mensagem exibida. Não apague bancos de outros trabalhos.

Depois de funcionar: leia **docs/GITHUB.md** para cumprir a publicação obrigatória e **docs/APRESENTACAO.md** para preparar a demonstração.
