<?php

declare(strict_types=1);

namespace Forge\Model\Tests;

use Forge\Model\Tests\TestSupport\FormModel\Login;
use Forge\Model\Tests\TestSupport\FormModel\PropertyType;
use Forge\Model\Tests\TestSupport\FormModel\PropertyVisibility;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use StdClass;

final class FormModelAttributesTest extends TestCase
{
    public function testGetHint(): void
    {
        $formModel = new Login();
        $this->assertSame('Write your id or email.', $formModel->getHint('login'));
        $this->assertSame('Write your password.', $formModel->getHint('password'));
    }

    public function testGetHintException(): void
    {
        $formModel = new Login();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Attribute 'noExist' does not exist.");
        $formModel->getHint('noExist');
    }

    public function testGetHints(): void
    {
        $formModel = new PropertyVisibility();
        $this->assertSame([], $formModel->getHints());
    }

    public function testGetLabel(): void
    {
        $formModel = new Login();
        $this->assertSame('Login:', $formModel->getLabel('login'));
    }

    public function testGetLabelException(): void
    {
        $formModel = new Login();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Attribute 'noExist' does not exist.");
        $formModel->getLabel('noExist');
    }

    public function testGetLabels(): void
    {
        $formModel = new PropertyVisibility();
        $this->assertSame([], $formModel->getLabels());
    }

    public function testGetPlaceHolder(): void
    {
        $formModel = new Login();
        $this->assertSame('Write Username or Email.', $formModel->getPlaceHolder('login'));
        $this->assertSame('Write Password.', $formModel->getPlaceHolder('password'));
    }

    public function testGetPlaceException(): void
    {
        $formModel = new Login();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Attribute 'noExist' does not exist.");
        $formModel->getPlaceHolder('noExist');
    }

    public function testGetPlaceHolders(): void
    {
        $formModel = new PropertyVisibility();
        $this->assertSame([], $formModel->getPlaceHolders());
    }

    public function testGetRawData(): void
    {
        $formModel = new PropertyType();

        $formModel->setValue('array', [1, 2]);
        $this->assertIsArray($formModel->getRawData('array'));
        $this->assertSame([1, 2], $formModel->getRawData('array'));

        $formModel->setValue('bool', true);
        $this->assertIsBool($formModel->getRawData('bool'));
        $this->assertSame(true, $formModel->getRawData('bool'));

        $formModel->setValue('float', 1.2023);
        $this->assertIsFloat($formModel->getRawData('float'));
        $this->assertSame(1.2023, $formModel->getRawData('float'));

        $formModel->setValue('int', 1);
        $this->assertIsInt($formModel->getRawData('int'));
        $this->assertSame(1, $formModel->getRawData('int'));

        $formModel->setValue('object', new StdClass());
        $this->assertIsObject($formModel->getRawData('object'));
        $this->assertInstanceOf(StdClass::class, $formModel->getRawData('object'));

        $formModel->setValue('string', 'samdark');
        $this->assertIsString($formModel->getRawData('string'));
        $this->assertSame('samdark', $formModel->getRawData('string'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Undefined property: "Forge\Model\Tests\TestSupport\FormModel\PropertyType::noExist".'
        );
        $formModel->getRawData('noExist');
    }

    public function testHas(): void
    {
        $formModel = new Login();
        $this->assertTrue($formModel->has('login'));
        $this->assertTrue($formModel->has('password'));
        $this->assertTrue($formModel->has('rememberMe'));
        $this->assertFalse($formModel->has('noExist'));
        $this->assertFalse($formModel->has('extraField'));
    }

    public function testSet(): void
    {
        $formModel = new PropertyType();

        $formModel->setValue('array', []);
        $this->assertIsArray($formModel->getRawData('array'));

        $formModel->setValue('bool', false);
        $this->assertIsBool($formModel->getRawData('bool'));

        $formModel->setValue('bool', 'false');
        $this->assertIsBool($formModel->getRawData('bool'));

        $formModel->setValue('float', 1.434536);
        $this->assertIsFloat($formModel->getRawData('float'));

        $formModel->setValue('float', '1.434536');
        $this->assertIsFloat($formModel->getRawData('float'));

        $formModel->setValue('int', 1);
        $this->assertIsInt($formModel->getRawData('int'));

        $formModel->setValue('int', '1');
        $this->assertIsInt($formModel->getRawData('int'));

        $formModel->setValue('object', new stdClass());
        $this->assertIsObject($formModel->getRawData('object'));

        $formModel->setValue('string', '');
        $this->assertIsString($formModel->getRawData('string'));
    }

    public function testSets(): void
    {
        $formModel = new PropertyType();

        // setValue attributes with array and to camel case disabled.
        $formModel->setValues(
            [
                'array' => [],
                'bool' => false,
                'float' => 1.434536,
                'int' => 1,
                'object' => new stdClass(),
                'string' => '',
            ],
        );

        $this->assertIsArray($formModel->getRawData('array'));
        $this->assertIsBool($formModel->getRawData('bool'));
        $this->assertIsFloat($formModel->getRawData('float'));
        $this->assertIsInt($formModel->getRawData('int'));
        $this->assertIsObject($formModel->getRawData('object'));
        $this->assertIsString($formModel->getRawData('string'));

        // setValue attributes with array and to camel case enabled.
        $formModel->setValues(
            [
                'array' => [],
                'bool' => 'false',
                'float' => '1.434536',
                'int' => '1',
                'object' => new stdClass(),
                'string' => '',
            ],
        );

        $this->assertIsArray($formModel->getRawData('array'));
        $this->assertIsBool($formModel->getRawData('bool'));
        $this->assertIsFloat($formModel->getRawData('float'));
        $this->assertIsInt($formModel->getRawData('int'));
        $this->assertIsObject($formModel->getRawData('object'));
        $this->assertIsString($formModel->getRawData('string'));
    }

    public function testSetsException(): void
    {
        $formModel = new PropertyType();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute "noExist" does not exist');
        $formModel->setValues(['noExist' => []]);
    }
}
