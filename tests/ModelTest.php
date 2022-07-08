<?php

declare(strict_types=1);

namespace Forge\Model\Tests;

use PHPUnit\Framework\TestCase;
use Forge\Model\Model as AbstractModel;
use Forge\Model\Tests\TestSupport\Error\CustomFormErrors;
use Forge\Model\Tests\TestSupport\Model\Model;

require __DIR__ . '/TestSupport/Model/NonNamespaced.php';

final class ModelTest extends TestCase
{
    public function testAttributes(): void
    {
        $model = new Model();
        $this->assertSame(['public', 'login', 'password'], $model->attributes());
    }

    public function testGetAttributeValue(): void
    {
        $model = new Model();
        $this->assertTrue($model->load(['Model' => ['login' => 'test', 'password' => 'test']]));
        $this->assertSame('test', $model->getAttributeValue('login'));
        $this->assertSame('test', $model->getAttributeValue('password'));
    }

    public function testGetData(): void
    {
        $model = new Model();
        $this->assertTrue($model->load(['Model' => ['login' => 'test', 'password' => 'test']]));
        $this->assertSame(['login' => 'test', 'password' => 'test'], $model->getData());
    }

    public function testGetFormName(): void
    {
        $model = new Model();
        $this->assertSame('Model', $model->getFormName());

        $model = new class () extends AbstractModel {
        };
        $this->assertSame('', $model->getFormName());

        $model = new \NonNamespaced();
        $this->assertSame('NonNamespaced', $model->getFormName());
    }

    public function testHas(): void
    {
        $model = new Model();
        $this->assertTrue($model->has('login'));
        $this->assertTrue($model->has('password'));
    }

    public function testIsEmpty(): void
    {
        $model = new Model();
        $this->assertTrue($model->isEmpty());
    }

    public function testLoad(): void
    {
        $model = new Model();
        $this->assertTrue($model->load(['Model' => ['login' => 'test', 'password' => 'test']]));
        $this->assertSame('test', $model->getAttributeValue('login'));
        $this->assertSame('test', $model->getAttributeValue('password'));
    }

    public function testLoadPublicField(): void
    {
        $model = new Model();
        $this->assertEmpty($model->public);

        $data = [
            'Model' => [
                'public' => 'samdark',
            ],
        ];

        $this->assertTrue($model->load($data));
        $this->assertSame('samdark', $model->public);
    }

    public function testLoadWithEmptyScope(): void
    {
        $model = new class () extends AbstractModel {
            private int $int = 1;
            private string $string = 'string';
            private float $float = 3.14;
            private bool $bool = true;
        };
        $model->load([
            'int' => '2',
            'float' => '3.15',
            'bool' => 'false',
            'string' => 555,
        ], '');
        $this->assertIsInt($model->getAttributeValue('int'));
        $this->assertIsFloat($model->getAttributeValue('float'));
        $this->assertIsBool($model->getAttributeValue('bool'));
        $this->assertIsString($model->getAttributeValue('string'));
    }

    public function testSet(): void
    {
        $model = new Model();
        $model->setValue('login', 'test');
        $model->setValue('password', 'test');
        $this->assertSame('test', $model->getAttributeValue('login'));
        $this->assertSame('test', $model->getAttributeValue('password'));
    }

    public function testSetFormErrors(): void
    {
        $formErrors = new CustomFormErrors();
        $model = new Model();
        $model->setFormErrors($formErrors);
        $this->assertSame($formErrors, $model->error());
    }
}
