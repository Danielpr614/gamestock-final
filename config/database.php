<?php
// Copie local.example.php para local.php para ajustar somente sua máquina.
$config=['host'=>'127.0.0.1','port'=>'3307','database'=>'gamestock_final','user'=>'root','password'=>''];
if (is_file(__DIR__.'/local.php')) $config=array_replace($config,require __DIR__.'/local.php');
return $config;
