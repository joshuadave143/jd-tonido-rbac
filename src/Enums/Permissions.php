<?php

namespace jdTonido\RBAC\Enums;

enum Permissions: string{
    case Add        = 'Add';
    case View       = 'View';
    case Edit       = 'Edit';
    case Delete     = 'Delete';
    case Allow      = 'Allow';
}