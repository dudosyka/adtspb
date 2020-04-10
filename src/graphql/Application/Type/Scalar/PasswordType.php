<?php
namespace GraphQL\Application\Type\Scalar;

use GraphQL\Error\Error;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\CustomScalarType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

class PasswordType extends ScalarType
{
    public static function create()
    {
        return new CustomScalarType([
            'name' => 'Password',
            'description' => 'Пароль',
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
        //TODO: сделать проверку пароля (пока так не работает)
        //пароль не должен содержать символов {}\[\]:\";'<>\/
//        if (preg_match("/[`{}\[\]:\";'<>\/]/", $value)) {
//            throw new \UnexpectedValueException("Cannot represent value as date: " . Utils::printSafe($value));
//        }
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
            throw new Error('Query error: Can only parse strings got: ' . $valueNode->kind, [$valueNode]);
        }
        if (preg_match("/[`{}\[\]:\";'<>\/]/", $valueNode->value)) {
            throw new Error("Not a valid password", [$valueNode]);
        }
        return $valueNode->value;
    }
}
