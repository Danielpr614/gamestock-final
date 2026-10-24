<?php
namespace App\Core;
final class View {
    public static function render(string $view,array $variables=[]): void {
        // O nome do parâmetro não pode ocupar a variável "data" dos formulários.
        extract($variables,EXTR_SKIP);$currentUser=Auth::user();
        $currentRoute=is_string($_GET['r']??null)?$_GET['r']:'home';require ROOT.'/app/Views/layout/header.php';require ROOT.'/app/Views/'.$view.'.php';require ROOT.'/app/Views/layout/footer.php';
    }
}
