> Rodada mais recente: [Revisão funcional de 24/10/2026](REVISAO_FUNCIONAL.md), com correções e resultados detalhados. Os testes abaixo registram as rodadas anteriores.

# Verificação da entrega

Foram executados 95 casos de integração em PHP 8.3, PDO MySQL e MariaDB 10.11, sobre uma instalação isolada. Os nomes exatos constam em testes_executados.json. Essa lista é evidência de execução; não é um runner automático desses testes distribuído no pacote.

Os casos incluem os cinco CRUDs, autorização administrativa, isolamento de pedidos de clientes, duplicidade de e-mail/categoria, parâmetros malformados, valores negativos, SQL injection, HTML escapado, CSRF, métodos HTTP, vínculos de exclusão, confirmação/cancelamento e duas compras concorrentes da última unidade. Também verificou atualização da base antiga sem perder contas, pedidos, preços ou estoques; reimportação sem duplicar jogos; cinco páginas sem repetição; páginas inválidas; filtros e ordenação preservados nos links.

O navegador automatizado verificou catálogo, 50 capas, cadastro, login, carrinho, checkout, telas administrativas, confirmação de senha no JavaScript e layout desktop/celular, carregamento da fonte local e imagens das cinco páginas. Também verificou troca de destaques por clique e teclado, destaque único selecionado, rotação de 7 segundos, ciclo dos quatro destaques, pausa/retomada, suspensão por mouse/foco e artes horizontais nos 50 cartões, jogos desativados fora dos destaques, catálogo vazio, resposta visual do carrinho, funcionamento dos links sem JavaScript e troca automática mantida, sem animações, com movimento reduzido. Os 20 prints foram capturados dessa execução após o término das transições. Não apareceram erros JavaScript nem falhas de carregamento dos arquivos necessários.

Além dos testes de integração, passaram 37 verificações de campos no teste local e a checagem de sintaxe de 42 arquivos PHP. O teste tests/validacao.php está incluído e pode ser repetido com PHP CLI, sem banco. Os testes de integração exigiram banco e servidor isolados no ambiente de desenvolvimento. Os dados de teste desses cenários não são o banco inicial entregue.

## Verificação manual no laboratório

- Catálogo exibe 50 jogos; todas as quatro plataformas têm produtos; filtro e busca combinam corretamente.
- Cadastro inválido e e-mail repetido retornam mensagem; criar conta não concede permissão administrativa.
- Sem login e com perfil cliente, não há acesso aos CRUDs administrativos.
- Login de Evandro permite consultar, criar, editar e excluir registros temporários nas cinco entidades, respeitando vínculos.
- Preço/estoque negativo, categoria ausente, imagem fora da pasta e link javascript são rejeitados.
- Confirmar um pedido baixa a quantidade correta; reenvio da mesma compra não cria duplicata.
- Cancelar uma compra confirmada devolve estoque uma vez; confirmado/concluído não é excluído.
- Sair remove acesso às telas protegidas.
- README, prints e diagramas aparecem no repositório público; link está correto.

## Limites

Nenhum conjunto de testes garante ausência de defeitos. A porta, a versão/configuração do PHP e do banco, a sessão e as permissões do laboratório devem ser conferidas no ambiente final. O cenário de concorrência testou duas compras da última unidade; não representa um teste de carga de produção. O limite de login é por sessão, não um sistema distribuído de proteção.

O checkout direto sem login foi recusado sem criar pedido. A seleção de jogos no carrinho como visitante continua permitida. As artes dos cartões foram verificadas quanto ao carregamento, proporção horizontal e preenchimento do espaço.

A revisão final reproduziu movimento reduzido desde a abertura da página, confirmou rotação contínua de quatro plataformas, pausa/retomada disponível e retomada após clicar com mouse e sair da área. O texto do intervalo e o rótulo Sem rotação foram retirados.

## Molduras das páginas de detalhes

Cada um dos 50 jogos foi aberto em 1440×1100 e em 390×844. As 100 verificações confirmaram imagem carregada, largura e altura compatíveis com a área interna da moldura (tolerância de 1 pixel), razão de aspecto preservada (tolerância de 0,2%), ausência de limite de altura e ausência de rolagem horizontal. Os resultados individuais, arquivos utilizados e dimensões estão em verificacao_50_detalhes.json. Três exemplos adicionais mostram Animal Crossing, Assetto Corsa e Astro Bot. A qualidade final depende da arte disponível; o sistema prioriza uma alternativa local maior quando existente.

## Correção do botão Buscar — 24/10/2026

A trava de envio duplicado foi limitada aos formulários POST. Buscas GET não desativam botões, pois a repetição da mesma URL pode resultar apenas em navegação ao fragmento do catálogo. A restauração de navegação atua somente nos botões desativados pela própria trava e preserva controles originalmente indisponíveis.

O JavaScript anterior reproduziu o bloqueio em teste isolado do ciclo de eventos. O script corrigido passou por 100 envios GET consecutivos, bloqueio de POST duplicado, restauração pageshow/pagehide, preservação de botões indisponíveis e callback de animação pendente durante a navegação. A sintaxe JavaScript foi verificada com Node. Esta verificação isolada não executou PHP ou banco e não substitui a confirmação no navegador da instalação do aluno.
