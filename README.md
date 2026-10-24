# GameStock — projeto final

**Integrante:** Daniel Pena  
**Tema:** loja de jogos para PC, PlayStation, Xbox e Nintendo.  
**Disciplina:** Linguagens de Programação.  
**Repositório público:** [https://github.com/Danielpr614/gamestock-final](https://github.com/Danielpr614/gamestock-final).

## Descrição

Sistema acadêmico de loja temática, desenvolvido com PHP 8, MySQL/PDO, orientação a objetos e MVC. Clientes consultam o catálogo, criam uma conta, montam um carrinho e confirmam pedidos. Administradores gerenciam usuários, clientes, jogos, categorias e pedidos em uma área autenticada.

O catálogo contém **50 jogos e 12 categorias**, com capas locais. Mantém os 25 jogos do projeto anterior e acrescenta 25 títulos de 2024 a 2026, conferidos em fontes oficiais. O sistema registra pedidos de demonstração: não processa pagamento, entrega de jogos ou integração comercial com as lojas externas. O tema ainda deve ser aprovado pelo professor, conforme o enunciado.

## Tecnologias

PHP 8; MySQL ou MariaDB do XAMPP; PDO; POO; MVC; HTML5; CSS; JavaScript puro. Sem framework, Composer, CDN ou instalação de Node para executar a aplicação. Os arquivos estáticos e as capas estão no projeto.

## Requisitos e etapas

| Entrega | Implementação | Arquivos ou acesso |
|---|---|---|
| Planejamento e regras | Tema, descrição, relacionamentos e regras | README e docs/REGRAS.md |
| UML e banco | Cinco classes de domínio; seis tabelas relacionadas | Diagramas abaixo; database/schema.sql |
| SQL completo | Banco, PK, FK, restrições e dados de teste | database/instalar.sql |
| MVC e PDO | Controllers, Models, Views, Repository e serviços | app/ e config/ |
| CRUDs | Usuario, Cliente, Game, Categoria, Pedido | Área administrativa |
| Autenticação | Login, logout, sessão, papéis e password_hash | app/Core/Auth.php e AuthController |
| Página pública | Catálogo com dados do banco | Página inicial |
| Registro de cliente | Nome, e-mail, telefone e senha | Criar conta |
| Extras | Busca, filtros, ordenação, detalhes, carrinho, estoque, dashboard e validação JS | Loja e administração |
| Relatório e prints | Este README e telas reais | docs/screenshots/ |
| GitHub | Repositório público com código, SQL, imagens e documentação | [Danielpr614/gamestock-final](https://github.com/Danielpr614/gamestock-final) |
| Apresentação | Roteiro e casos de demonstração | docs/APRESENTACAO.md |

As datas antigas do enunciado não foram usadas como prazo deste pacote.

## Atualizar a versão já instalada

Se o GameStock Final com 25 jogos já funciona, siga [ATUALIZAR.md](ATUALIZAR.md). Use **database/atualizar_50_jogos.sql**, que acrescenta os títulos e preserva os dados atuais. Não use instalar.sql sobre uma instalação existente.

A nova interface usa grafite e azul, fonte Inter local, letras maiores, capas com fundo suave, paginação e interações discretas. O destaque principal apresenta quatro jogos ativos do banco, um por plataforma e permite trocar por clique ou teclado, com rotação automática a cada 7 segundos e botão Pausar/Retomar. A página do jogo tem arte de fundo e área de compra destacada. Os textos principais e preços foram ampliados para facilitar a apresentação em sala. A legibilidade no projetor deve ser conferida na própria sala.

## Instalação no XAMPP

1. Extraia o ZIP. Se baixou pelo GitHub, renomeie a pasta **gamestock-final-main** para **gamestock_final**. Copie a pasta **gamestock_final** para `C:\xampp\htdocs\gamestock_final`. O arquivo `index.php` precisa ficar diretamente dentro dessa pasta.
2. Abra o painel do XAMPP e inicie **Apache** e **MySQL**.
3. Abra `http://localhost/phpmyadmin/`. Se o Apache usa 8080, abra `http://localhost:8080/phpmyadmin/`.
4. Na página inicial do phpMyAdmin, clique em **Importar**. Selecione `gamestock_final/database/instalar.sql` e execute. O script cria o banco **gamestock_final**, sem modificar `loja_games`.
5. A porta padrão configurada neste pacote para MySQL é **3307**, usada no computador de origem. Confira a porta do MySQL no painel do XAMPP. Se for 3306 ou outra, copie `config/local.example.php` para `config/local.php` e ajuste `port`. Ajuste usuário e senha do banco apenas se o laboratório usar credenciais diferentes.
6. Abra `http://localhost/gamestock_final/` ou, com Apache em 8080, `http://localhost:8080/gamestock_final/`.
7. Confirme que a loja exibe **50 jogos**, faça login como administrador e teste os cadastros.

**Apache 8080 é a porta do site; MySQL 3306/3307 é a porta do banco.** São configurações independentes. As rotas usam `index.php?r=...` e não exigem configurar reescrita de URLs.

O banco usa InnoDB e utf8mb4. Ambiente efetivamente testado: PHP 8.3 com PDO MySQL e MariaDB 10.11. O XAMPP normalmente fornece MariaDB no botão MySQL. Para MySQL, prefira 8.0.16 ou superior, que aplica as restrições CHECK do esquema.

Se já existir um banco `gamestock_final`, faça um backup e confira sua origem antes de reinstalar. O script inicial não apaga tabelas: importar duas vezes pode gerar erro de tabela existente. Não exclua bancos de outros projetos.

Leia [COMECE_AQUI.md](COMECE_AQUI.md) para o passo a passo curto e [solução de problemas](docs/INSTALACAO.md) para portas, pasta duplicada e transporte ao laboratório.

O carrinho público pode ser montado sem login e fica associado à sessão do navegador. Isso não cria pedido nem baixa estoque. A confirmação exige uma conta de cliente autenticada.

## Contas de demonstração

| Perfil | E-mail | Senha |
|---|---|---|
| Administrador — Evandro | evandro@gmail.com | admin123 |
| Cliente — Pedro | pedro@gmail.com | Pedro123! |

São contas fictícias de avaliação, com senhas armazenadas como hash. Não use esses dados em um serviço real nem cadastre dados pessoais no material público de entrega.

Os destaques preferidos são Clair Obscur: Expedition 33 (PC), God of War Ragnarök (PlayStation), Forza Horizon 5 (Xbox) e Mario Kart World (Nintendo). Se algum estiver desativado, a seleção usa outro jogo ativo da mesma plataforma.

## Cinco classes e CRUDs

As cinco classes concretas herdam os métodos CRUD de `Entity`. `Repository` realiza as consultas preparadas. Controllers validam requisições, verificam autorização e escolhem a View. `AccountService` e `OrderService` coordenam operações que precisam de transações em várias tabelas.

| Classe | Criar | Consultar | Atualizar | Excluir |
|---|---|---|---|---|
| Usuario | Conta de admin ou cliente | Lista e formulário | Dados, senha e situação | Conta sem vínculos; própria conta protegida |
| Cliente | Conta e telefone juntos | Lista e formulário | Dados cadastrais | Cliente sem pedidos e sua conta |
| Game | Jogo com categoria | Lista, busca e detalhes | Dados, imagem, preço e estoque | Jogo sem histórico de pedidos |
| Categoria | Nome e descrição | Lista e formulário | Nome e descrição | Categoria sem jogos vinculados |
| Pedido | Rascunho ou compra de cliente | Lista e detalhes | Itens do rascunho e transições permitidas | Rascunho ou cancelado |

As restrições de exclusão preservam os vínculos do banco e o histórico. Pedidos confirmados ou concluídos não são apagados. Jogos e contas podem ser desativados.

## Diagrama de classes

```mermaid
classDiagram
 direction TB
 class Entity {
  <<abstract>>
  +listar() array
  +buscarPorId(id) array
  +cadastrar(dados) int
  +atualizar(id, dados) void
  +excluir(id) void
 }
 class Usuario {
  +porEmail(email) array
 }
 class Cliente {
  +porUsuario(id) array
  +listarDetalhes() array
 }
 class Game {
  +catalogo(filtros) array
  +destaques() array
  +detalhes(id) array
 }
 class Categoria
 class Pedido {
  +itens(id) array
  +porUsuario(id) array
 }
 Entity <|-- Usuario
 Entity <|-- Cliente
 Entity <|-- Game
 Entity <|-- Categoria
 Entity <|-- Pedido
 Usuario "1" --> "0..1" Cliente : conta
 Categoria "1" --> "0..*" Game : classifica
 Cliente "1" --> "0..*" Pedido : realiza
 Pedido "0..*" --> "1..*" Game : itens
```

Os métodos CRUD mostrados em Entity são herdados pelas cinco classes principais. O relacionamento Pedido–Game é materializado por `itens_pedido`; essa tabela não conta como uma sexta classe principal. [Fonte do diagrama](docs/diagrama_classes.mmd).

## Modelo do banco

```mermaid
erDiagram
 usuarios ||--o| clientes : possui
 clientes ||--o{ pedidos : realiza
 categorias ||--o{ games : classifica
 pedidos ||--|{ itens_pedido : contem
 games ||--o{ itens_pedido : referencia
 usuarios {
  int id PK
  varchar nome
  varchar email UK
  varchar senha
  enum perfil
  boolean ativo
 }
 clientes {
  int id PK
  int usuario_id FK,UK
  varchar telefone
 }
 categorias {
  int id PK
  varchar nome UK
  varchar descricao
 }
 games {
  int id PK
  int categoria_id FK
  varchar titulo
  enum plataforma
  decimal preco
  int estoque
  varchar imagem
  boolean ativo
 }
 pedidos {
  int id PK
  int cliente_id FK
  enum status
  decimal total
  char chave UK
 }
 itens_pedido {
  int id PK
  int pedido_id FK
  int game_id FK
  int quantidade
  decimal preco_unitario
  varchar titulo
  varchar plataforma
 }
```

O diagrama destaca as chaves e atributos principais. O esquema completo, incluindo descrições, links, referências de preço, datas e índices, está em [database/schema.sql](database/schema.sql).

## Regras de negócio e validações

- Um e-mail identifica uma conta; o cadastro normaliza espaços e letras maiúsculas. O banco também garante unicidade. Dois endereços no mesmo campo são rejeitados.
- O cadastro público sempre cria um cliente. Somente administradores acessam os cinco CRUDs.
- Senhas têm 8 a 72 bytes, confirmação no cadastro público e hash. Alterações sem nova senha preservam o hash anterior.
- Categoria vinculada, cliente com pedidos e jogo usado em pedidos não podem ser excluídos. Nomes de categoria e combinações título/plataforma são únicos.
- Preço e estoque não podem ser negativos. Valores monetários usam centavos inteiros no cálculo e DECIMAL no banco. O servidor calcula o pedido a partir dos preços cadastrados.
- Confirmar pedido baixa o estoque em transação e bloqueia as linhas dos jogos. Cancelar um pedido confirmado devolve o estoque uma única vez. Cancelar um rascunho não altera estoque.
- Rascunhos podem ser editados. Se o preço de um jogo mudar, salve o rascunho novamente antes de confirmar. Pedidos concluídos e cancelados são finais.
- A chave de confirmação impede que repetir a mesma compra crie outro pedido. Clientes só veem e cancelam os próprios pedidos.
- O administrador não pode excluir, desativar ou rebaixar sua própria conta. O serviço preserva ao menos um administrador ativo.
- Requisições que alteram dados usam POST e token CSRF. A interface escapa HTML, os links externos aceitam HTTP/HTTPS e as imagens são nomes de arquivos locais permitidos.
- A sessão expira após 30 minutos de inatividade. A situação da conta é consultada nas páginas protegidas. Login tem limite de tentativas por sessão; não é um bloqueio global por IP.

Detalhes em [docs/REGRAS.md](docs/REGRAS.md).

## Organização

- `index.php`: entrada única e tabela de rotas.
- `app/Core`: conexão, autenticação, validação e renderização.
- `app/Models`: cinco entidades e consultas de domínio.
- `app/Repositories`: operações PDO compartilhadas.
- `app/Services`: regras transacionais de contas e pedidos.
- `app/Controllers`: requisições públicas e administrativas.
- `app/Views`: telas, formulários e layout.
- `config`: configuração padrão e exemplo de configuração local.
- `database`: esquema, dados iniciais e instalador SQL.
- `public/assets`: CSS, JavaScript, logo e capas locais.
- `docs`: planejamento, diagramas, guias, testes e prints.
- `tests`: testes de validação executáveis sem banco.

## Funcionalidades extras

Busca literal por título; filtros combinados por categoria e plataforma; ordenação; paginação de 12 jogos por página; detalhes do jogo; carrinho em sessão; registro e histórico do cliente; controle transacional de estoque; cancelamento com reposição; dashboard; formulários com validação JavaScript e PHP; apresentação responsiva.

Os cartões e botões têm efeitos suaves de foco, passagem do mouse e clique. Ao adicionar um jogo, a confirmação e o contador do carrinho recebem destaque visual após sucesso no servidor. A rotação pausa durante passagem do mouse, foco do teclado ou aba oculta. Cliques com mouse não deixam o timer preso ao foco dos botões. O botão de pausa/retomada tem ícone e nome acessível, sem texto sobre o intervalo na página. Com preferência de movimento reduzido, as imagens continuam trocando, sem animação; sem JavaScript, o primeiro destaque e os links de navegação continuam acessíveis. Não há reprodução automática de vídeos.

Os 50 jogos iniciais têm artes horizontais específicas em public/assets/images/cards para preencher o catálogo. Nos detalhes, a moldura acompanha a proporção da arte escolhida, sem altura mínima, cortes ou deformação. Quando já existe uma versão local significativamente maior, ela é priorizada para melhorar a nitidez. [Fontes dessas artes](docs/fontes_artes_horizontais.json). Para novos jogos, prefira uma arte horizontal; o sistema usa a imagem cadastrada quando não existe uma arte específica de cartão.

Imagens são selecionadas entre arquivos locais. Para adicionar uma capa, copie um arquivo JPG, PNG ou WebP para `public/assets/images` e selecione seu nome no formulário de jogo. Este pacote **não implementa upload de arquivos pela interface**.

## Testes e evidências

**Revisão de 24/10/2026:** concluída com 253 verificações de backend/banco/sintaxe e 192 verificações no navegador, além das 37 validações de campos e 8 verificações de renderização. Foram corrigidos o bloqueio das buscas, o preenchimento de formulários de edição, a página de erro de rota inválida e o seletor de capas. Consulte [a revisão funcional](docs/REVISAO_FUNCIONAL.md) e [os resultados detalhados](docs/revisao_2026-10-24.json). Os registros abaixo documentam também as rodadas anteriores.

Foram executadas **95 verificações de integração** sobre banco MariaDB e servidor PHP, além de testes de navegação em navegador real, cadastro, checkout, carregamento das capas e ausência de rolagem horizontal em celular. A revisão das molduras conferiu os 50 jogos em desktop e celular (100 telas), validando preenchimento, proporção original e carregamento das imagens. Consulte [o registro de verificação](docs/verificacao_50_detalhes.json). A disputa entre duas compras pela última unidade confirmou somente uma compra, com estoque final zero.

A lista de verificações e os limites da avaliação estão em [docs/TESTES.md](docs/TESTES.md). O arquivo [docs/testes_executados.json](docs/testes_executados.json) contém os casos efetivamente executados. Os testes não garantem ausência de qualquer defeito ou substituem a avaliação no computador do laboratório.

Teste local de validações, sem alterar o banco:

```bat
cd C:\xampp\htdocs\gamestock_final
C:\xampp\php\php.exe tests\validacao.php
```

## Prints das principais telas

### Loja pública
![Catálogo](docs/screenshots/01-catalogo.png)
![Detalhes do jogo](docs/screenshots/02-detalhes.png)
![Carrinho](docs/screenshots/03-carrinho.png)
![Cadastro do cliente](docs/screenshots/04-cadastro.png)
![Pedido do cliente](docs/screenshots/05-pedido.png)
![Login](docs/screenshots/06-login.png)

### Administração
![Dashboard](docs/screenshots/07-dashboard.png)
![CRUD de jogos](docs/screenshots/08-admin-jogos.png)
![CRUD de categorias](docs/screenshots/09-admin-categorias.png)
![CRUD de clientes](docs/screenshots/10-admin-clientes.png)
![CRUD de usuários](docs/screenshots/11-admin-usuarios.png)
![CRUD de pedidos](docs/screenshots/12-admin-pedidos.png)
![Formulário de jogo](docs/screenshots/13-formulario-jogo.png)
![Formulário de pedido](docs/screenshots/14-formulario-pedido.png)
![Tela no celular](docs/screenshots/15-mobile.png)
![Cartões e filtros do novo catálogo](docs/screenshots/16-catalogo-cartoes.png)
![Destaque interativo](docs/screenshots/17-destaque-interativo.png)
![Animal Crossing — moldura proporcional](docs/screenshots/18-detalhes-animal-crossing.png)
![Assetto Corsa — arte de maior definição](docs/screenshots/19-detalhes-assetto-corsa.png)
![Astro Bot — imagem completa](docs/screenshots/20-detalhes-astro-bot.png)

Os prints usam dados de demonstração adicionados durante os testes. A instalação inicial terá dois usuários, um cliente e nenhum pedido; o dashboard muda conforme o uso.

## Catálogo e referências

Os 25 jogos originais preservam os preços consultados em **08/10/2026**. Os 25 novos títulos têm referências brasileiras consultadas em **11/10/2026**, obtidas no Steam, PlayStation, Nintendo e catálogo público oficial da Microsoft. O preço consultado pode ser promocional ou regular conforme a fonte, e não se atualiza automaticamente. Estoques são fictícios para demonstração. A compra usa o preço interno atual do banco.

Datas de lançamento se referem à plataforma e edição cadastradas: por exemplo, Final Fantasy VII Rebirth no PC (2025) e Ghost of Yōtei — Edição Completa no PS5 (2026). A seleção exclui versões com lançamento posterior a 11/10/2026.

As capas pertencem aos respectivos titulares e são usadas como referência acadêmica. Consulte [fontes originais](docs/fontes_catalogo.json), [fontes dos 25 novos títulos](docs/fontes_catalogo_atualizacao.json) e [lista da ampliação](docs/CATALOGO_50.md). A fonte Inter está incluída localmente sob a licença SIL Open Font License, em public/assets/fonts/LICENSE.txt.

Referências técnicas: [PDO](https://www.php.net/manual/pt_BR/book.pdo.php), [password_hash](https://www.php.net/manual/pt_BR/function.password-hash.php), [sessões](https://www.php.net/manual/pt_BR/book.session.php), [bloqueio de linhas InnoDB](https://dev.mysql.com/doc/refman/8.4/en/innodb-locking-reads.html).

## Pendências externas à implementação

Compartilhar o link do repositório com o professor, confirmar a aprovação do tema e realizar a apresentação. Quando a equipe estiver definida, adicionar os demais integrantes ao README. Não publicar `config/local.php`, dados reais de clientes ou backups com informações pessoais.
