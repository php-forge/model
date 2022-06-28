<?php

declare(strict_types=1);

namespace Forge\Model\Tests\TestSupport\Model;

use Forge\Model\Model as AbstractModel;

final class Model extends AbstractModel
{
    public string $public = '';
    private null|string $login = null;
    private null|string $password = null;
}
