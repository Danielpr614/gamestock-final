# Atualizar seu GameStock Final — 50 jogos e tema azul

Estas instruções são para Daniel, que já tem a versão com 25 jogos funcionando em casa. Não precisa reinstalar XAMPP nem criar outro banco.

## Se você já aplicou a atualização para 50 jogos

Para esta revisão de molduras dos detalhes, capas e rotação automática, copie somente **app** e **public** do ZIP novo para C:\xampp\htdocs\gamestock_final, substituindo os arquivos. Atualize também README.md e docs para a entrega. Deixe config como está. Pressione Ctrl + F5 no site. **Não precisa importar nenhum SQL novamente.**

Os cartões usam artes horizontais; o banner muda a cada 7 segundos. Passe o mouse ou mantenha o foco do teclado no destaque para pausar temporariamente; use Pausar/Retomar para controlar a rotação. A preferência de movimento reduzido desativa apenas as animações; a troca automática continua. Os quatro destaques representam PC, PlayStation, Xbox e Nintendo, com controle de pausa por ícone.

## 1. Guardar uma cópia

- No phpMyAdmin, selecione **gamestock_final**, clique em Exportar, mantenha Rápido/SQL e exporte. Guarde como **gamestock_final_antes_50.sql** fora da pasta pública do projeto.
- Copie **C:\xampp\htdocs\gamestock_final** para a Área de Trabalho. Renomeie essa cópia para **gamestock_final_antes_50**. Copie, sem mover a pasta original.

## 2. Atualizar arquivos

- Extraia este novo ZIP em uma pasta de Downloads, fora de htdocs.
- Abra a pasta **gamestock_final** extraída. Copie as pastas **app**, **public**, **database**, **docs** e **tests**, além de **README.md**, **ATUALIZAR.md** e **COMECE_AQUI.md**.
- Cole em **C:\xampp\htdocs\gamestock_final**, aceitando substituir os arquivos com o mesmo nome.
- Não substitua sua pasta **config**. Assim a configuração do banco desta máquina continua como estava. O restante do projeto já instalado é compatível com a atualização.

## 3. Acrescentar jogos

- Com Apache e MySQL iniciados, abra o phpMyAdmin.
- Selecione **gamestock_final** e clique em **Importar**.
- Selecione **C:\xampp\htdocs\gamestock_final\database\atualizar_50_jogos.sql** e execute.
- Este arquivo não apaga tabelas, usuários ou pedidos. Acrescenta 25 jogos e as categorias necessárias. Não altera preços e estoques antigos. Se importar novamente, os jogos já existentes não serão duplicados.
- Não importe instalar.sql ou seed.sql sobre a base existente: esses dois arquivos são para instalação inicial.

## 4. Conferir

Abra **http://localhost/gamestock_final/** (ou localhost:8080 se essa for a porta Apache). Pressione **Ctrl + F5** para atualizar o navegador.

A página deve mostrar o tema grafite/azul, textos maiores, destaque com três jogos selecionáveis, efeitos suaves nos cartões e **50 jogos encontrados** se você não acrescentou outros jogos. São 12 por página, com cinco páginas no catálogo inicial.

Na base inicial: PC 22, PlayStation 9, Xbox 9, Nintendo 10. Jogos desativados não entram na contagem pública. Seus próprios cadastros alteram essas quantidades normalmente.

Login continua **evandro@gmail.com / admin123**, salvo se você alterou a senha. Os dados já cadastrados permanecem.

Depois de conferir as telas, retomamos os testes administrativos e a publicação no GitHub. README e prints deste ZIP já correspondem à nova interface.

Esta versão já reúne os 50 jogos e o acabamento visual. Não é preciso instalar uma versão intermediária. As alterações de apresentação mantêm as rotas, tokens CSRF, autenticação, CRUDs e regras de pedidos/estoque.

A tela de detalhes agora ajusta a moldura à imagem automaticamente, preservando a proporção e todo o conteúdo da arte. Este ajuste já foi verificado para os 50 jogos em desktop e celular. Não altera o banco.

## Correção do botão Buscar (24/10/2026)

Copie `public/assets/js/app.js` e `app/Views/layout/header.php` desta versão para os mesmos caminhos da instalação, substituindo os dois arquivos. Pressione Ctrl+F5 no navegador. Não importe SQL e não altere a configuração do banco. A atualização impede que buscas repetidas deixem o botão Buscar desativado.

## Revisão funcional completa (24/10/2026)

Copie as pastas `app`, `public`, `docs` e `tests`, além de `README.md` e `ATUALIZAR.md`, sobre os mesmos caminhos da instalação atual. Substitua os arquivos. Preserve `config` e o banco existente. Não importe SQL. Pressione Ctrl+F5 no navegador. Confira uma busca repetida e a edição de um registro existente: seus campos devem aparecer preenchidos.
