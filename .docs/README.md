# PHPdoc

## Content

- [Extension - how to use](#usage)
- [Configuration - how to configure](#configuration)

## Usage

First of all, register `PhpDocExtension`.

```yaml
extensions:
    phpdoc: Contributte\PhpDoc\DI\PhpDocExtension
```

## Configuration

As you can see, that configuration is by default.

```yaml
phpdoc:
  ignore: 
    - persistent
    - serializationVersion
      
  cache: auto
```

You can add more ignored annotations or change cache adapter.
