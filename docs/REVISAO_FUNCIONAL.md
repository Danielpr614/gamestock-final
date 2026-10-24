# Revisão funcional — 24/10/2026

Revisão da versão mais recente em instalação isolada, sem acesso aos dados do computador do aluno. Datas antigas do enunciado não foram utilizadas.

## Problemas identificados e corrigidos

1. **Busca bloqueada:** formulários GET eram tratados pela mesma trava de envio duplicado dos formulários POST. Repetir a URL podia apenas navegar ao fragmento do catálogo, sem recarga ou evento pageshow. Agora consultas GET continuam habilitadas; a trava de alteração de dados foi mantida para POST. A restauração só reabilita botões bloqueados pela própria trava.
2. **Edição com campos vazios:** o parâmetro `$data` de `View::render` colidia com a variável `data` das telas; `extract(..., EXTR_SKIP)` impedia entregar o registro ao formulário. O parâmetro foi renomeado. Usuários, clientes, categorias e jogos agora recebem os dados; pedidos preservam cliente e quantidades.
3. **Erro ao renderizar URL malformada:** o cabeçalho voltava a validar uma rota inválida enquanto a página de erro era renderizada. Agora o cabeçalho usa uma rota de apresentação segura, permitindo concluir a resposta de erro.
4. **Diretório no seletor de capas:** a pasta `cards` aparecia como opção de imagem e era recusada ao salvar. Agora o seletor lista apenas arquivos JPG, JPEG, PNG e WebP.

## Execução

- **253 verificações de backend, banco e sintaxe**, incluindo 42 arquivos PHP e sintaxe JavaScript. O teste de campos incluído passou suas 37 asserções.
- **192 verificações no navegador**, incluindo 100 telas de detalhes (50 jogos em desktop e celular), buscas repetidas, categorias, ordenação, carrossel, cadastro, login, carrinho, checkout, campos preenchidos na edição, retenção de dados após erro, confirmação de exclusão cancelada e navegação Voltar.
- **8 verificações de renderização**, executáveis sem banco em `tests/renderizacao.php`.
- Teste isolado do JavaScript reproduziu o bloqueio anterior e confirmou 100 envios GET consecutivos na correção, manutenção da trava POST e restauração de navegação.
- Duas instâncias PHP concorrentes disputaram a última unidade: uma compra confirmada, outra recusada e estoque final zero.
- Nenhuma falha nas verificações finais, nenhum erro JavaScript registrado e nenhum erro fatal PHP no log da execução final.

Os casos de backend incluem os cinco CRUDs; validações e duplicidades; acesso por perfil; sessão expirada; conta desativada; parâmetros inválidos; métodos HTTP; CSRF; estoque; total calculado no servidor; reenvio idempotente; cancelamento com reposição única; vínculos de exclusão; loja vazia e destaque alternativo. O relatório detalhado está em `revisao_2026-10-24.json`.

## Repetir verificações sem banco

No terminal, dentro da pasta do projeto:

```bat
C:\xampp\php\php.exe tests\validacao.php
C:\xampp\php\php.exe tests\renderizacao.php
```

Esses dois arquivos não criam, alteram ou excluem registros. Os testes de integração e navegador foram executados no ambiente isolado de revisão; os respectivos executores internos não estão incluídos no pacote, pois recriam o banco descartável.

## Limites

O navegador de teste usa Chromium 131, PHP 8.3.6 e MariaDB 10.11.14. Não é o computador do aluno nem o laboratório. O servidor PHP embutido não aplica `.htaccess`: a proteção de arquivos do Apache foi inspecionada, mas não executada nesta rodada. Não houve teste de carga nem acesso a GitHub nesta revisão. Um teste curto no XAMPP de destino continua necessário; não é preciso repetir manualmente toda a bateria. Nenhuma revisão garante ausência absoluta de bugs.
