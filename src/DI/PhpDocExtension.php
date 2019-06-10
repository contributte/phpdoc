<?php declare(strict_types = 1);

namespace Contributte\PhpDoc\DI;

use Contributte\DI\Helper\ExtensionDefinitionsHelper;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\PhpFileCache;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\InvalidStateException;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpLiteral;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
class PhpDocExtension extends CompilerExtension
{

	private const CACHE_AUTO = 'auto';

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'ignore' => Expect::listOf('string')->default([
				'persistent',
				'serializationVersion',
			]),
			'cache' => Expect::anyOf(Expect::string(), Expect::array(), Expect::type(Statement::class))->default(self::CACHE_AUTO),
			'temp' => Expect::string(),
			'debug' => Expect::bool(false),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		$annotationsDefinition = $builder->addDefinition($this->prefix('annotations'))
			->setType(AnnotationReader::class)
			->setAutowired(false);

		foreach ($config->ignore as $name) {
			$annotationsDefinition->addSetup('addGlobalIgnoredName', [$name]);
		}

		$cachePrefix = $this->prefix('cache');
		if ($config->cache === self::CACHE_AUTO) {
			$cacheDefinition = $builder->addDefinition($cachePrefix)
				->setAutowired(false);
			if (extension_loaded('apcu')) {
				$cacheDefinition->setType(ApcuCache::class);
			} else {
				if ($config->temp === null) {
					throw new InvalidStateException('Please provide "%s > cache" or "%s > temp", we cannot find suitable cache storage automatically.');
				}

				$cacheDefinition->setType(PhpFileCache::class)
					->setArguments([$config->temp]);
			}
		} else {
			$definitionsHelper = new ExtensionDefinitionsHelper($this->compiler);
			$cacheDefinition = $definitionsHelper->getDefinitionFromConfig($config->cache, $cachePrefix);
		}

		$builder->addDefinition($this->prefix('reader'))
			->setType(Reader::class)
			->setFactory(CachedReader::class, [
				$annotationsDefinition,
				$cacheDefinition,
				$config->debug,
			]);
	}

	public function afterCompile(ClassType $class): void
	{
		$init = $class->getMethod('initialize');
		$init->addBody('?::registerLoader("class_exists");', [new PhpLiteral(AnnotationRegistry::class)]);
	}

}
