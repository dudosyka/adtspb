<?php
namespace GraphQL\Application\Entity;


use GraphQL\Utils\Utils;

/**
 * Class EntityBase
 * Базовая сущность, содержащая id.
 *
 * @package GraphQL\Application\Entity
 */
abstract class EntityBase
{
	public int $id;

	public function __construct(array $data = null)
	{
		if($data != null)
			Utils::assign($this, $data);
	}

	abstract public function __getTable();

}