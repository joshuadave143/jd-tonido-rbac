<?php

namespace jdTonido\RBAC\database;

use jdTonido\RBAC\database\TableCheckAndCreate;

class Initialize extends TableCheckAndCreate{
    public function __construct(public $pgsqlInstance){
        parent::__construct($pgsqlInstance);

        $this->run();
    }
}