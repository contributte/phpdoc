<?php

namespace Contributte\PhpDoc\DI;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\PhpFileCache;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpLiteral;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class PhpDocExtension extends CompilerExtension
{

	const CACHE_AUTO = 'auto';

	/** @var array */
	public $defaults = [
		'ignore' => [
			'persistent',
			'serializationVersion',
		],
		'cache' => self::CACHE_AUTO,
		'temp' => '%tempDir%',
		'debug' => '%debugMode%',
	];

	/**
	 * Register services
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);
		$config = Helpers::expand($config, $builder->parameters);

		$annotations = $builder->addDefinition($this->prefix('annotations'))
			->setClass(AnnotationReader::class)
			->setAutowired(FALSE);

		foreach ($config['ignore'] as $name) {
			$annotations->addSetup('addGlobalIgnoredName', [$name]);
		}

		$cache = $builder->addDefinition($this->prefix('cache'))
			->setAutowired(FALSE);

		if ($config['cache'] === self::CACHE_AUTO) {
			if (extension_loaded('apcu')) {
				$cache->setClass(ApcuCache::class);
			} else {
				$cache->setClass(PhpFileCache::class, [$config['temp']]);
			}
		} else {
			Compiler::loadDefinition($cache, $config['cache']);
		}

		$builder->addDefinition($this->prefix('reader'))
			->setClass(Reader::class)
			->setFactory(CachedReader::class, [
				'@' . $this->prefix('annotations'),
				'@' . $this->prefix('cache'),
				$config['debug'],
			]);
	}

	/**
	 * Modify init method
	 *
	 * @param ClassType $class
	 * @return void
	 */
	public function afterCompile(ClassType $class)
	{
		$init = $class->getMethod('initialize');
		$init->addBody('?::registerLoader("class_exists");', [new PhpLiteral(AnnotationRegistry::class)]);
	}

}
