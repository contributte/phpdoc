<?php declare(strict_types = 1);

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

class PhpDocExtension extends CompilerExtension
{

	private const CACHE_AUTO = 'auto';

	/** @var mixed[] */
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
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);
		$config = Helpers::expand($config, $builder->parameters);

		$annotations = $builder->addDefinition($this->prefix('annotations'))
			->setType(AnnotationReader::class)
			->setAutowired(false);

		foreach ($config['ignore'] as $name) {
			$annotations->addSetup('addGlobalIgnoredName', [$name]);
		}

		$cache = $builder->addDefinition($this->prefix('cache'))
			->setAutowired(false);

		if ($config['cache'] === self::CACHE_AUTO) {
			if (extension_loaded('apcu')) {
				$cache->setType(ApcuCache::class);
			} else {
				$cache->setType(PhpFileCache::class)
					->setArguments([$config['temp']]);
			}
		} else {
			Compiler::loadDefinition($cache, $config['cache']);
		}

		$builder->addDefinition($this->prefix('reader'))
			->setType(Reader::class)
			->setFactory(CachedReader::class, [
				'@' . $this->prefix('annotations'),
				'@' . $this->prefix('cache'),
				$config['debug'],
			]);
	}

	/**
	 * Modify init method
	 */
	public function afterCompile(ClassType $class): void
	{
		$init = $class->getMethod('initialize');
		$init->addBody('?::registerLoader("class_exists");', [new PhpLiteral(AnnotationRegistry::class)]);
	}

}
