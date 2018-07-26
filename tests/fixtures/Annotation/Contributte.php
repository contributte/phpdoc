<?php declare(strict_types = 1);

namespace Tests\Fixtures\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Contributte
{

	/** @var string @Required */
	public $package;

}
