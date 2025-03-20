<?php
namespace jdTonido\RBAC\core\Traits;

use jdTonido\RBAC\core\Views\UnauthorizedPage;

trait Views
{
    public function UnauthorizedPage($message = "You do not have permission to access this page."){
        echo UnauthorizedPage::render($message);
        exit();
    }
}