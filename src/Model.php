<?php

declare(strict_types=1);

namespace Forge\Model;

use Closure;
use InvalidArgumentException;
use Forge\Model\Contract\ModelContract;
use Forge\Model\Contract\ModelErrorsContract;
use Yiisoft\Strings\Inflector;

use function array_key_exists;
use function array_keys;
use function explode;
use function is_subclass_of;
use function property_exists;
use function str_contains;
use function strrchr;
use function substr;

abstract class Model implements ModelContract
{
    private ?ModelErrorsContract $modelErrors = null;
    private ?Inflector $inflector = null;
    private ModelType $modelTypes;
    private array $rawData = [];

    public function __construct()
    {
        $this->modelTypes = new ModelType($this);
    }

    public function attributes(): array
    {
        return array_keys($this->modelTypes->attributes());
    }

    public function error(): ModelErrorsContract
    {
        return match (empty($this->modelErrors)) {
            true => $this->modelErrors = new ModelErrors(),
            false => $this->modelErrors,
        };
    }

    public function getCastValue(string $attribute): mixed
    {
        return $this->readProperty($attribute);
    }

    /**
     * @return string Returns classname without a namespace part or empty string when class is anonymous
     */
    public function getFormName(): string
    {
        if (str_contains(static::class, '@anonymous')) {
            return '';
        }

        $className = strrchr(static::class, '\\');
        if ($className === false) {
            return static::class;
        }

        return substr($className, 1);
    }

    public function getRawData(string $value = ''): mixed
    {
        return match (empty($value)) {
            true => $this->rawData,
            false => $this->rawData[$value] ?? $this->getCastValue($value),
        };
    }

    public function has(string $attribute): bool
    {
        [$attribute, $nested] = $this->getNested($attribute);

        return $nested !== null || array_key_exists($attribute, $this->modelTypes->attributes());
    }

    public function isEmpty(): bool
    {
        return empty($this->rawData);
    }

    /**
     * @param array $data
     * @param string|null $formName
     *
     * @return bool
     */
    public function load(array $data, ?string $formName = null): bool
    {
        $this->error()->clear();

        $this->rawData = [];
        $scope = $formName ?? $this->getFormName();

        /** @psalm-var array<string, string> */
        $this->rawData = match (empty($scope)) {
            true => $data,
            false => $data[$scope] ?? [],
        };

        foreach ($this->rawData as $name => $value) {
            $this->setValue($name, $value);
        }

        return $this->rawData !== [];
    }

    public function setFormErrors(ModelErrorsContract $modelErrors): void
    {
        $this->modelErrors = $modelErrors;
    }

    public function setValue(string $name, mixed $value): void
    {
        [$realName] = $this->getNested($name);

        /** @var mixed */
        $valueTypeCast = $this->modelTypes->phpTypeCast($realName, $value);

        $this->writeProperty($name, $valueTypeCast);
    }

    public function setValues(array $data): void
    {
        /**
         * @var array<string, mixed> $data
         * @var mixed $value
         */
        foreach ($data as $name => $value) {
            $name = $this->getInflector()->toCamelCase($name);

            if ($this->has($name)) {
                $this->setValue($name, $value);
            } else {
                throw new InvalidArgumentException(sprintf('Attribute "%s" does not exist', $name));
            }
        }
    }

    public function types(): ModelType
    {
        return $this->modelTypes;
    }

    protected function getInflector(): Inflector
    {
        return match (empty($this->inflector)) {
            true => $this->inflector = new Inflector(),
            false => $this->inflector,
        };
    }

    protected function getNestedValue(string $method, string $attribute): string
    {
        $result = '';

        [$attribute, $nested] = $this->getNested($attribute);

        if ($nested !== null) {
            /** @var ModelContract $attributeNestedValue */
            $attributeNestedValue = $this->getCastValue($attribute);
            /** @var string */
            $result = $attributeNestedValue->$method($nested);
        }

        return $result;
    }

    /**
     * @return string[]
     *
     * @psalm-return array{0: string, 1: null|string}
     */
    private function getNested(string $attribute): array
    {
        if (!str_contains($attribute, '.')) {
            return [$attribute, null];
        }

        [$attribute, $nested] = explode('.', $attribute, 2);
        $attributeNested = $this->modelTypes->getType($attribute);

        if (!is_subclass_of($attributeNested, self::class)) {
            throw new InvalidArgumentException("Attribute \"$attribute\" is not a nested attribute.");
        }

        if (!property_exists($attributeNested, $nested)) {
            throw new InvalidArgumentException("Undefined property: \"$attributeNested::$nested\".");
        }

        return [$attribute, $nested];
    }

    private function readProperty(string $attribute): mixed
    {
        $class = static::class;

        [$attribute, $nested] = $this->getNested($attribute);

        if (!property_exists($class, $attribute)) {
            throw new InvalidArgumentException("Undefined property: \"$class::$attribute\".");
        }

        /** @psalm-suppress MixedMethodCall */
        $getter = static function (ModelContract $class, string $attribute, ?string $nested): mixed {
            return match ($nested) {
                null => $class->$attribute,
                default => $class->$attribute->getCastValue($nested),
            };
        };

        $getter = Closure::bind($getter, null, $this);

        /** @var Closure $getter */
        return $getter($this, $attribute, $nested);
    }

    private function writeProperty(string $attribute, mixed $value): void
    {
        [$attribute, $nested] = $this->getNested($attribute);

        /** @psalm-suppress MixedMethodCall */
        $setter = static function (ModelContract $class, string $attribute, mixed $value, ?string $nested): void {
            match ($nested) {
                null => $class->$attribute = $value,
                default => $class->$attribute->setValue($nested, $value),
            };
        };

        $setter = Closure::bind($setter, null, $this);

        /** @var Closure $setter */
        $setter($this, $attribute, $value, $nested);
    }
}
