# Regras e planejamento

**Tema:** loja de jogos multiplataforma. **Responsável:** Daniel Pena.

## Contas e clientes

Usuario representa a autenticação; Cliente complementa uma conta com telefone. E-mail é único em toda a tabela de usuários, normalizado para minúsculas. O cadastro público não aceita promover a conta a administrador. Nome, e-mail, telefone e senha são validados no servidor. Campos inesperados não entram no SQL. Cliente tem conta única; criação e edição dos dois registros usam transação.

A conta não pode excluir ou rebaixar a si mesma na administração. O serviço trava os administradores ao alterar a situação para preservar ao menos um ativo. Conta vinculada a cliente é removida pelo CRUD de clientes, quando não há pedidos. Se uma conta vinculada for promovida a administrador, seus dados ficam preservados e devem ser gerenciados no CRUD de usuários.

## Produtos e categorias

Categoria tem nome único. Game referencia uma categoria existente; título e plataforma juntos são únicos. Estoque é inteiro entre 0 e 1.000.000; preço de 0 a 999.999,99; quantidades de compra entre 1 e 99 por jogo. Links externos aceitam apenas HTTP/HTTPS e capas são selecionadas entre nomes locais seguros.

Jogos desativados não aparecem no catálogo nem podem ser comprados. A tela pública mostra o preço interno e uma observação sobre os dados de referência. Alterar estoque é permitido para reposição/acerto; o histórico de pedidos mantém quantidades e preços próprios.

## Pedidos

Pedido tem cliente, chave de confirmação única, status, total e um ou mais itens. Item guarda o jogo referenciado e uma cópia do título, plataforma e preço da compra. Preços antigos permanecem no histórico após editar um jogo.

Rascunho pode ser editado e não reserva estoque. Confirmar baixa estoque atomicamente; concluir preserva a baixa. Cancelar confirmado repõe estoque, cancelar rascunho não repõe. Não é possível voltar do cancelado/concluído. Somente rascunhos e cancelados podem ser excluídos. Para confirmar um rascunho cujo preço mudou, o administrador precisa salvar novamente os itens.

O cancelamento que ultrapassaria o limite de estoque é recusado; o administrador deve ajustar a reposição antes de tentar novamente. Clientes autenticados e ativos só consultam os próprios pedidos. Compras repetidas com a mesma chave retornam o pedido já criado.

## Integridade e limites

Chaves estrangeiras impedem apagar jogos, clientes e categorias com vínculos protegidos. O número ID é uma identificação, não a posição na lista: exclusões podem deixar intervalos normalmente.

Trata-se de simulação acadêmica. Não há pagamento, entrega digital, frete, recuperação de senha por e-mail, upload pela interface ou sincronização automática de preços externos. Esses recursos não são exigidos no enunciado.
