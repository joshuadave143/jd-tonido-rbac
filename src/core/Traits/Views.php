<?php
namespace jdTonido\RBAC\core\Traits;

use jdTonido\RBAC\core\Views\UnauthorizedPage;

trait Views
{
    public function UnauthorizedPage($message = "You do not have permission to access this page.", $url = '/'){
        echo UnauthorizedPage::render($message, $url);
        exit();
    }
}