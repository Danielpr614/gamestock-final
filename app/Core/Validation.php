<?php
namespace App\Core;
use InvalidArgumentException;
final class Validation {
    public static function text(array $data,string $key,int $max,bool $required=true): string {
        if (isset($data[$key]) && !is_string($data[$key])) throw new InvalidArgumentException('Formato inválido para '.$key.'.');
        $v=preg_replace('/^[\s\p{Z}]+|[\s\p{Z}]+$/u','',$data[$key]??'');
        if ($v===null) throw new InvalidArgumentException('Texto inválido.');
        if (!preg_match('//u',$v) || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/',$v)) throw new InvalidArgumentException('Caracteres inválidos em '.$key.'.');
        preg_match_all('/./us',$v,$matches);
        if (($required && $v==='') || count($matches[0])>$max) throw new InvalidArgumentException('Confira o campo '.$key.' (máximo '.$max.' caracteres).');
        return $v;
    }
    public static function email(array $d): string {
        $v=strtolower(self::text($d,'email',150));
        if (!filter_var($v,FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Informe um único e-mail válido.'); return $v;
    }
    public static function password(array $d,bool $required=true): string {
        $v=$d['senha']??'';
        if (!is_string($v) || (($required || $v!=='') && (strlen($v)<8 || strlen($v)>72 || trim($v)==='' || str_contains($v,"\0")))) throw new InvalidArgumentException('A senha deve ter entre 8 e 72 bytes e não pode ser composta só por espaços.'); return $v;
    }
    public static function integer($v,int $min=1,int $max=2147483647): int {
        if ((!is_string($v) && !is_int($v)) || !preg_match('/^\d+$/D',(string)$v)) throw new InvalidArgumentException('Informe um número inteiro válido.');
        $n=filter_var($v,FILTER_VALIDATE_INT,['options'=>['min_range'=>$min,'max_range'=>$max]]);
        if ($n===false) throw new InvalidArgumentException('Valor fora do intervalo permitido.'); return $n;
    }
    public static function money($v): int {
        if (!is_string($v) || !preg_match('/^\d{1,6}([.,]\d{1,2})?$/D',$v)) throw new InvalidArgumentException('Preço inválido: use até duas casas decimais, sem separador de milhar.');
        $parts=explode('.',str_replace(',','.',$v)); return (int)$parts[0]*100+(int)str_pad($parts[1]??'',2,'0');
    }
    public static function decimal(int $cents): string { return intdiv($cents,100).'.'.str_pad((string)($cents%100),2,'0',STR_PAD_LEFT); }
    public static function phone(array $d): string {
        $v=self::text($d,'telefone',25); if (!preg_match('/^[0-9()+ .-]+$/D',$v)) throw new InvalidArgumentException('Telefone inválido.');
        $v=preg_replace('/\D/','',$v); if (!in_array(strlen($v),[10,11],true)) throw new InvalidArgumentException('Informe telefone com DDD e 10 ou 11 dígitos.'); return $v;
    }
    public static function choice($v,array $options): string { if (!is_string($v) || !in_array($v,$options,true)) throw new InvalidArgumentException('Opção inválida.'); return $v; }
    public static function url(array $d): string {
        $v=self::text($d,'link_loja',500,false);
        if ($v!=='' && (!filter_var($v,FILTER_VALIDATE_URL) || !in_array(strtolower((string)parse_url($v,PHP_URL_SCHEME)),['https','http'],true) || parse_url($v,PHP_URL_USER) || parse_url($v,PHP_URL_PASS))) throw new InvalidArgumentException('O link precisa ser um endereço HTTP ou HTTPS válido.'); return $v;
    }
}
