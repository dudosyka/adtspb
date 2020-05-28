<?php
namespace GraphQL\Application\Type\Scalar;

use GraphQL\Error\Error;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\CustomScalarType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

class YesNoType extends ScalarType
{

    public array $whitelist = [
        'да',
        'нет'
    ];

    public static function create()
    {
        return new CustomScalarType([
            'name' => 'YesNo',
            'serialize' => [__CLASS__, 'serialize'],
            'parseValue' => [__CLASS__, 'parseValue'],
            'parseLiteral' => [__CLASS__, 'parseLiteral'],
        ]);
    }

    /**
     * Serializes an internal value to include in a response.
     *
     * @param string $value
     * @return string
     */
    public function serialize($value)
    {
        // Assuming internal representation of email is always correct:
        return $value;

        // If it might be incorrect and you want to make sure that only correct values are included in response -
        // use following line instead:
        // return $this->parseValue($value);
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param mixed $value
     * @return mixed
     */
    public function parseValue($value)
    {

        if (!in_array($value, $this->whitelist)) {
            throw new \UnexpectedValueException("Немогу запарсить значение как да/нет: " . Utils::printSafe($value));
        }


        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input
     *
     * @param \GraphQL\Language\AST\Node $valueNode
     * @param array|null $variables
     * @return string
     * @throws Error
     */
    public function parseLiteral($valueNode, array $variables = null)
    {
        // Note: throwing GraphQL\Error\Error vs \UnexpectedValueException to benefit from GraphQL
        // error location in query:
        if (!$valueNode instanceof StringValueNode) {
            throw new Error('Ошибка запроса: могу запарсить только строки, получил: ' . $valueNode->kind, [$valueNode]);
        }

        if (!in_array($valueNode->value, $this->whitelist)) {
            throw new Error("Некорректное значение да/нет", [$valueNode]);
        }

        return $valueNode->value;
    }
}
