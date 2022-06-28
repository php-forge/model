<?php

declare(strict_types=1);

namespace Forge\Model\Tests\TestSupport\FormModel;

use Forge\Model\FormModel;

final class Nested extends FormModel
{
    private ?int $id = null;
    private Login $user;

    public function __construct()
    {
        $this->user = new Login();

        parent::__construct();
    }

    public function getLabels(): array
    {
        return [
            'id' => 'Id',
        ];
    }

    public function getHints(): array
    {
        return [
            'id' => 'Readonly ID',
        ];
    }
}
