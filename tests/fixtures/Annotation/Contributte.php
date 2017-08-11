<?php

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
