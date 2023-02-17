# The Aristonet EntityToModelBundle

The Aristonet EntityToModelBundle is the fastest way to convert
doctrine entities to typescript models.

## Install

To install the library, use [Composer](https://getcomposer.org/), the PHP package manager:

    composer require aristonet/entity-to-model-bundle

## Usage

```console
php bin/console convert:entitytomodel
```

```console
php bin/console convert:entitytomodel --modelDir=path/to/models
```

```console
php bin/console convert:entitytomodel --className=Order
```

```console
php bin/console convert:entitytomodel --className=Order --modelDir=path/to/models
```

## Author

Niculae Niculae
