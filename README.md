![](https://heatbadger.now.sh/github/readme/contributte/phpdoc/?deprecated=1)

<p align=center>
    <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
    <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
    <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
    Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

## Disclaimer

| :warning: | This project is no longer being maintained. Please use [nettrine/annotations](https://github.com/nettrine/annotations).
|---|---|

| Composer | [`contributte/phpdoc`](https://packagist.org/contributte/phpdoc) |
|---| --- |
| Version | ![](https://badgen.net/packagist/v/contributte/phpdoc) |
| PHP | ![](https://badgen.net/packagist/php/contributte/phpdoc) |
| License | ![](https://badgen.net/github/license/contributte/phpdoc) |

## Versions

| State       | Version | Branch   | PHP      |
|-------------|---------|----------|----------|
| dev         | `^0.3`  | `master` | `>= 7.1` |
| stable      | `^0.2`  | `master` | `>= 7.1` |
| stable      | `^0.1`  | `master` | `>= 5.6` |

## Usage

### Setup

Register `PhpDocExtension`.

```neon
extensions:
    phpdoc: Contributte\PhpDoc\DI\PhpDocExtension
```

### Configuration

```neon
phpdoc:
  # ignored annotations
  ignore:
    - persistent
    - serializationVersion

  # override default cache (default is apcu if available, php file otherwise)
  cache: Doctrine\Common\Cache\ApcuCache()
```


## Development

This package was maintain by these authors.

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners.html) **contributte** development team.
Also thank you for being used this package.
