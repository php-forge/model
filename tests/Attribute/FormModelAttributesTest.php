<?php

declare(strict_types=1);

namespace Forge\Model\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use Forge\Model\Attribute\FormModelAttributes;
use Forge\Model\Contract\FormModelContract;
use Forge\Model\FormModel;
use Forge\Model\Tests\TestSupport\FormModel\Login;

final class FormModelAttributesTest extends TestCase
{
    public function testGetHint(): void
    {
        $formModel = new Login();
        $this->assertSame('Write your id or email.', FormModelAttributes::getHint($formModel, 'login'));

        $anonymousForm = new class () extends FormModel {
            private string $age = '';
        };
        $this->assertEmpty(FormModelAttributes::getHint($anonymousForm, 'age'));
    }

    public function testGetInputId(): void
    {
        $formModel = new Login();
        $this->assertSame('login-login', FormModelAttributes::getInputId($formModel, 'login'));
    }

    /**
     * @dataProvider dataGetInputName
     *
     * @param FormModelContract $formModel
     * @param string $attribute
     * @param string $expected
     */
    public function testGetInputName(FormModelContract $formModel, string $attribute, string $expected): void
    {
        $this->assertSame($expected, FormModelAttributes::getInputName($formModel, $attribute));
    }

    public function testGetInputNameException(): void
    {
        $anonymousForm = new class () extends FormModel {
        };

        $this->expectExceptionMessage('formName() cannot be empty for tabular inputs.');
        FormModelAttributes::getInputName($anonymousForm, '[0]dates[0]');
    }

    public function testGetLabel(): void
    {
        $formModel = new Login();
        $this->assertSame('Login:', FormModelAttributes::getLabel($formModel, 'login'));
    }

    public function testGetName(): void
    {
        $formModel = new Login();
        $this->assertSame('login', FormModelAttributes::getName($formModel, '[0]login'));
        $this->assertSame('login', FormModelAttributes::getName($formModel, 'login[0]'));
        $this->assertSame('login', FormModelAttributes::getName($formModel, '[0]login[0]'));
    }

    public function testGetNameException(): void
    {
        $formModel = new Login();
        $this->expectExceptionMessage("Attribute 'noExist' does not exist.");
        FormModelAttributes::getName($formModel, 'noExist');
    }

    public function testGetNameInvalid(): void
    {
        $formModel = new Login();
        $this->expectExceptionMessage('Attribute name must contain word characters only.');
        FormModelAttributes::getName($formModel, 'content body');
    }

    public function testGetPlaceHolder(): void
    {
        $formModel = new Login();
        $this->assertSame('Write Username or Email.', FormModelAttributes::getPlaceHolder($formModel, 'login'));
        $this->assertSame('Write Password.', FormModelAttributes::getPlaceHolder($formModel, 'password'));
    }

    public function dataGetInputName(): array
    {
        $loginModel = new Login();
        $anonymousModel = new class () extends FormModel {
        };

        return [
            [$loginModel, '[0]content', 'Login[0][content]'],
            [$loginModel, 'dates[0]', 'Login[dates][0]'],
            [$loginModel, '[0]dates[0]', 'Login[0][dates][0]'],
            [$loginModel, 'age', 'Login[age]'],
            [$anonymousModel, 'dates[0]', 'dates[0]'],
            [$anonymousModel, 'age', 'age'],
        ];
    }

    public function testGetValue(): void
    {
        $formModel = new Login();
        $this->assertNull(FormModelAttributes::getValue($formModel, 'login'));
    }

    public function testMultibyteGetName(): void
    {
        $formModel = new class () extends FormModel {
            private string $?????? = '';
        };
        $this->assertSame('??????', FormModelAttributes::getName($formModel, '[0]??????'));
        $this->assertSame('??????', FormModelAttributes::getName($formModel, '??????[0]'));
        $this->assertSame('??????', FormModelAttributes::getName($formModel, '[0]??????[0]'));
    }

    public function testMultibyteGetInputId(): void
    {
        $formModel = new class () extends FormModel {
            private string $m??kA = '';
        };
        $this->assertSame('m??ka', FormModelAttributes::getInputId($formModel, 'm??kA'));
    }
}
