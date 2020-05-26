<?php
namespace GraphQL\Application\Type\Scalar;

use GraphQL\Error\Error;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\CustomScalarType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

class DateType extends ScalarType
{
    public static function create()
    {
        return new CustomScalarType([
            'name' => 'Date',
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
        if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $value)) {
            throw new \UnexpectedValueException("Немогу запарсить значение как дату: " . Utils::printSafe($value));
        }

        if (($timestamp = strtotime($value)) === false) {
            throw new \UnexpectedValueException("Немогу запарсить значение как дату: " . Utils::printSafe($value));
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

        if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $valueNode->value)) {
            throw new Error("Некорректная дата", [$valueNode]);
        }

        if (($timestamp = strtotime($valueNode)) === false) {
            throw new Error("Некорректная дата", [$valueNode]);
        }

        return $valueNode->value;
    }
}
