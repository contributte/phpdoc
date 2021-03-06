# Contributte PHPdoc

## Content

- [Setup](#setup)
- [Configuration](#configuration)

## Setup

```bash
composer require contributte/phpdoc
```

Register `PhpDocExtension`.

```yaml
extensions:
    phpdoc: Contributte\PhpDoc\DI\PhpDocExtension
```

## Configuration

```yaml
phpdoc:
  # ignored annotations
  ignore:
    - persistent
    - serializationVersion

  # override default cache (default is apcu if available, php file otherwise)
  cache: Doctrine\Common\Cache\ApcuCache()
```
