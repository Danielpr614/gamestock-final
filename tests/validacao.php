<?php
// Testes de campos isolados; não conecta nem modifica o banco de dados.
require dirname(__DIR__).'/app/Core/Validation.php';
use App\Core\Validation as V;
$count=0;
function check(string $name,callable $test): void {
    global $count;
    try { if (!$test()) throw new RuntimeException('Resultado inesperado.'); $count++; echo "OK: $name\n"; }
    catch (Throwable $e) { fwrite(STDERR,"FALHOU: $name: ".$e->getMessage()."\n"); exit(1); }
}
function rejects(callable $f): bool { try {$f();} catch (InvalidArgumentException $e) {return true;} return false; }
check('E-mail normalizado',fn()=>V::email(['email'=>'  DANIEL@example.com '])==='daniel@example.com');
foreach (['a@b.com,c@d.com','sem-arroba','a@b.com a@c.com'] as $s) check('E-mail inválido '.$s,fn()=>rejects(fn()=>V::email(['email'=>$s])));
check('E-mail em array',fn()=>rejects(fn()=>V::email(['email'=>['a@b.com']])));
check('Texto vazio',fn()=>rejects(fn()=>V::text(['nome'=>'  '],'nome',100)));
check('Espaços Unicode',fn()=>rejects(fn()=>V::text(['nome'=>"\u{00A0}"],'nome',100)));
check('Texto Unicode válido',fn()=>V::text(['nome'=>' João '],'nome',4)==='João');
check('Texto muito longo',fn()=>rejects(fn()=>V::text(['nome'=>'João'],'nome',3)));
check('Caractere de controle',fn()=>rejects(fn()=>V::text(['nome'=>"abc\0"],'nome',100)));
check('Telefone com DDD',fn()=>V::phone(['telefone'=>'(38) 99999-1234'])==='38999991234');
check('Telefone incompleto',fn()=>rejects(fn()=>V::phone(['telefone'=>'123'])));
check('Telefone com letras',fn()=>rejects(fn()=>V::phone(['telefone'=>'3899999abcd'])));
check('Senha válida',fn()=>V::password(['senha'=>'Pedro123!'])==='Pedro123!');
check('Senha curta',fn()=>rejects(fn()=>V::password(['senha'=>'123'])));
check('Senha muito longa',fn()=>rejects(fn()=>V::password(['senha'=>str_repeat('a',73)])));
check('Senha só espaços',fn()=>rejects(fn()=>V::password(['senha'=>'        '])));
check('Senha opcional vazia',fn()=>V::password([],false)==='');
check('Centavos com vírgula',fn()=>V::money('129,99')===12999);
check('Centavos com ponto',fn()=>V::money('129.9')===12990);
check('Preço zero',fn()=>V::money('0')===0);
check('Formatação decimal',fn()=>V::decimal(12999)==='129.99');
foreach (['-1','1e3','1.000,00','1.234','1000000'] as $s) check('Preço inválido '.$s,fn()=>rejects(fn()=>V::money($s)));
check('Estoque inteiro',fn()=>V::integer('20',0,1000000)===20);
foreach (['-1','2.5','abc','1000001'] as $s) check('Estoque inválido '.$s,fn()=>rejects(fn()=>V::integer($s,0,1000000)));
check('Opção permitida',fn()=>V::choice('PC',['PC','Xbox'])==='PC');
check('Opção inválida',fn()=>rejects(fn()=>V::choice('admin',['cliente'])));
check('URL HTTPS',fn()=>V::url(['link_loja'=>'https://store.steampowered.com/'])==='https://store.steampowered.com/');
check('URL javascript',fn()=>rejects(fn()=>V::url(['link_loja'=>'javascript:alert(1)'])));
check('URL com credenciais',fn()=>rejects(fn()=>V::url(['link_loja'=>'https://user:secret@example.com/'])));
echo "\n$count verificações aprovadas.\n";
