<?php

declare(strict_types=1);

namespace Forge\Model\Tests\Attribute;

use Forge\Model\Attribute\FormErrorsAttributes;
use Forge\Model\Tests\TestSupport\FormModel\Login;
use Forge\Model\Tests\TestSupport\TestTrait;
use PHPUnit\Framework\TestCase;

final class FormErrorsAttributesTest extends TestCase
{
    use TestTrait;

    public function testClearAllErrors(): void
    {
        $formModel = new Login();
        $formModel->error()->add('login', 'Login is required.');
        $formModel->error()->add('password', 'Password is required.');
        $this->assertSame(
            ['login' => ['Login is required.'], 'password' => ['Password is required.']],
            FormErrorsAttributes::getAll($formModel),
        );

        $formModel->error()->clear();
        $this->assertEmpty(FormErrorsAttributes::getAll($formModel));
    }

    public function testClearForAttribute(): void
    {
        $formModel = new Login();
        $formModel->error()->add('login', 'Login is required.');
        $formModel->error()->add('password', 'Password is required.');
        $this->assertSame(
            ['login' => ['Login is required.'], 'password' => ['Password is required.']],
            FormErrorsAttributes::getAll($formModel),
        );

        $formModel->error()->clear('login');
        $this->assertSame(['password' => ['Password is required.']], FormErrorsAttributes::getAll($formModel));
    }

    public function testGet(): void
    {
        $formModel = new Login();
        $formModel->error()->add('login', 'Login is required.');
        $formModel->error()->add('password', 'Password is required.');
        $this->assertSame(['Login is required.'], FormErrorsAttributes::get($formModel, 'login'));
        $this->assertSame(['Password is required.'], FormErrorsAttributes::get($formModel, 'password'));
    }

    public function testGetAll(): void
    {
        $formModel = new Login();
        $formModel->error()->add('login', 'Login is required.');
        $formModel->error()->add('password', 'Password is required.');
        $this->assertSame(
            ['login' => ['Login is required.'], 'password' => ['Password is required.']],
            FormErrorsAttributes::getAll($formModel),
        );
    }

    public function testGetFirstEmpty(): void
    {
        $formModel = new Login();
        $this->assertEmpty(FormErrorsAttributes::getFirst($formModel, 'login'));
    }

    public function testGetFirstsEmpty(): void
    {
        $formModel = new Login();
        $this->assertEmpty(FormErrorsAttributes::getFirsts($formModel));
    }

    public function testGetSummary(): void
    {
        $formModel = new Login();
        $formModel->error()->add('login', 'Login is required.');
        $formModel->error()->add('password', 'Password is required.');
        $this->assertSame(
            ['Login is required.', 'Password is required.'],
            FormErrorsAttributes::getSummary($formModel),
        );
    }

    public function testGetSummaryFirst(): void
    {
        $formModel = new Login();
        $formModel->error()->add('login', 'Login is required.');
        $formModel->error()->add('password', 'Password is required.');
        $this->assertSame(
            ['login' => 'Login is required.', 'password' => 'Password is required.'],
            FormErrorsAttributes::getSummaryFirst($formModel),
        );
    }

    public function testGetSummaryOnlyAttributes(): void
    {
        $formModel = new Login();
        $formModel->error()->add('login', 'This value is not a valid email address.');
        $formModel->error()->add('password', 'Is too short.');
        $this->assertSame(
            ['This value is not a valid email address.'],
            FormErrorsAttributes::getSummary($formModel, ['login']),
        );
        $this->assertSame(
            ['Is too short.'],
            FormErrorsAttributes::getSummary($formModel, ['password']),
        );
    }

    public function testHas(): void
    {
        $formModel = new Login();
        $formModel->error()->add('login', 'Login is required.');
        $formModel->error()->add('password', 'Password is required.');
        $this->assertTrue(FormErrorsAttributes::has($formModel, 'login'));
        $this->assertTrue(FormErrorsAttributes::has($formModel, 'password'));
        $this->assertFalse(FormErrorsAttributes::has($formModel, 'email'));
        $this->assertTrue(FormErrorsAttributes::has($formModel));
    }
}
