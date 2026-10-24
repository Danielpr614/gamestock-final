# Roteiro de apresentação

1. Apresente a loja GameStock e Daniel Pena como integrante. Mostre catálogo, filtros por plataforma/categoria e busca.
2. Abra detalhes de um jogo e explique preços internos e links de referência.
3. Crie um cliente fictício; mostre que e-mail repetido, dois e-mails e senha curta são recusados.
4. Entre como cliente, adicione um jogo ao carrinho e confirme. Mostre histórico e que o estoque baixou.
5. Cancele o pedido e confira reposição; repetir o cancelamento não devolve duas vezes.
6. Entre como Evandro. Mostre dashboard e os cinco CRUDs. Para demonstrar exclusão, use uma categoria e um jogo temporários sem pedidos.
7. Crie um pedido em rascunho, altere os itens e confirme. Explique por que pedidos confirmados não podem ser apagados.
8. No VS Code, mostre index.php (rotas), Controller, Model, Repository, Service e View. Explique a separação com suas palavras.
9. No phpMyAdmin mostre tabelas, chaves estrangeiras e senhas com hash, sem divulgar dados pessoais.
10. Abra o repositório público com README, diagramas, SQL e prints.

## Explicações para estudar

- PDO prepara as consultas e separa valores dos comandos SQL.
- MVC separa dados, requisições e apresentação. Os serviços coordenam regras que envolvem mais de uma tabela.
- Cliente e Usuario são distintos: um guarda dados de cliente, outro identifica a conta autenticada.
- A validação do JavaScript ajuda a preencher, mas PHP e banco garantem as regras mesmo se o navegador for manipulado.
- A transação confirma todas as alterações ou desfaz todas; o bloqueio de estoque impede duas compras da mesma última unidade.
- A chave de confirmação evita duplicar compra quando o navegador reenvia a requisição.

Treine os cadastros com dados fictícios, confira as instruções no computador do laboratório e exporte seu banco para guardar as alterações. Não é necessário ocultar a origem do código: consiga explicar e demonstrar o funcionamento de cada parte.
