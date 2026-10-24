<?php
// Regressão das telas: usa dados fictícios e não acessa nem modifica o banco.
require dirname(__DIR__).'/app/bootstrap.php';
$_SESSION=[];
use App\Core\View;
$checks=[];
function renderTest(string $view,array $variables): string {
    ob_start();
    try { View::render($view,$variables);return ob_get_contents(); }
    finally { ob_end_clean(); }
}
foreach (['usuarios','clientes','categorias','games'] as $entity) {
    $_GET=['r'=>'admin/'.$entity.'/form'];
    $html=renderTest('admin/form',['title'=>'Editar','entity'=>$entity,'id'=>42,'data'=>['nome'=>'Nome de teste','email'=>'teste@example.com','telefone'=>'38999991111','titulo'=>'Jogo de teste','descricao'=>'Descrição de teste','preco'=>'12.34','estoque'=>3,'plataforma'=>'Xbox','sistemas'=>'Xbox Series','ativo'=>1,'perfil'=>'cliente'],'categories'=>[]]);
    $checks['Dados preenchidos: '.$entity]=str_contains($html,'value="'.($entity==='games'?'Jogo de teste':'Nome de teste').'"');
    if ($entity==='games') $checks['Seletor de capas sem diretórios']=!str_contains($html,'value="cards"');
}
$_GET=['r'=>'admin/pedidos/form'];$_SESSION['admin_order_key']=str_repeat('a',64);
$html=renderTest('admin/order_form',['title'=>'Editar pedido','id'=>42,'data'=>['cliente_id'=>7],'clients'=>[['id'=>7,'nome'=>'Cliente de teste','email'=>'teste@example.com']],'games'=>[['id'=>8,'titulo'=>'Jogo de teste','plataforma'=>'PC','preco'=>'12.34','estoque'=>3]],'quantities'=>[8=>2]]);
$checks['Cliente selecionado no rascunho']=str_contains($html,'value="7" selected');
$checks['Quantidade preenchida no rascunho']=str_contains($html,'name="itens[8]" value="2"');
$_GET=['r'=>['invalido']];
$html=renderTest('error',['title'=>'Confira os dados','message'=>'Formato inválido de parâmetro.']);
$checks['Erro de rota malformada renderiza até o fim']=str_contains($html,'Formato inválido de parâmetro.')&&str_contains($html,'</html>');
foreach ($checks as $name=>$ok) {echo ($ok?'OK: ':'FALHOU: ').$name."\n";if (!$ok) exit(1);}
echo count($checks)." verificações de renderização aprovadas.\n";
