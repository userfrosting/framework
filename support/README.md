# Support module for UserFrosting 4

[![Latest Version](https://img.shields.io/github/release/userfrosting/support.svg)](https://github.com/userfrosting/support/releases)
![PHP Version](https://img.shields.io/packagist/php-v/userfrosting/support.svg?color=brightgreen)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Join the chat at https://chat.userfrosting.com/channel/support](https://chat.userfrosting.com/api/v1/shield.svg?name=UserFrosting)](https://chat.userfrosting.com/channel/support)
[![Donate](https://img.shields.io/badge/Open%20Collective-Donate-blue.svg)](https://opencollective.com/userfrosting#backer)

| Branch | Build | Coverage | Style |
| ------ |:-----:|:--------:|:-----:|
| [master][Support]  | [![][support-master-build]][support-travis] | [![][support-master-codecov]][support-codecov] | [![][support-style-master]][support-style] |
| [develop][support-develop] | [![][support-develop-build]][support-travis] | [![][support-develop-codecov]][support-codecov] | [![][support-style-develop]][support-style] |

<!-- Links -->
[Support]: https://github.com/userfrosting/support
[support-develop]: https://github.com/userfrosting/support/tree/develop
[support-version]: https://img.shields.io/github/release/userfrosting/support.svg
[support-master-build]: https://github.com/userfrosting/support/workflows/Build/badge.svg?branch=master
[support-master-codecov]: https://codecov.io/gh/userfrosting/support/branch/master/graph/badge.svg
[support-develop-build]: https://github.com/userfrosting/support/workflows/Build/badge.svg?branch=develop
[support-develop-codecov]: https://codecov.io/gh/userfrosting/support/branch/develop/graph/badge.svg
[support-releases]: https://github.com/userfrosting/support/releases
[support-travis]: https://github.com/userfrosting/support/actions
[support-codecov]: https://codecov.io/gh/userfrosting/support
[support-style-master]: https://github.styleci.io/repos/60137523/shield?branch=master&style=flat
[support-style-develop]: https://github.styleci.io/repos/60137523/shield?branch=develop&style=flat
[support-style]: https://github.styleci.io/repos/60137523

This module contains support classes for UserFrosting and related modules.

## Exception

`userfrosting/support` provides a number of custom exception types used by the main UserFrosting project:

- RuntimeException
  - FileNotFoundException
  - JsonException
- HttpException
  - BadRequestException
  - ForbiddenException
  - NotFoundException

### HttpException

A large portion of UserFrosting's exception types inherit from the `HttpException` class.

The `HttpException` class acts like a typical exception, but it maintains two additional parameters internally: a list of messages (`UserMessage`) that the exception handler may display to the client, and a status code that should be returned with the response.  As a simple example, consider the `BadRequestException`:

```php
<?php
class BadRequestException extends HttpException
{
    protected $defaultMessage = 'Bad data!';
    protected $httpErrorCode = 400;
}
```

It defines a default message, `'Bad data!'`, that a registered exception handler can display on an error page or push to the alert stream.  It also sets a default HTTP status code to return with the error response.

The default message can be overridden when the exception is thrown in your code:

```php
$e = new BadRequestException("This is the exception message that will be logged for the dev/sysadmin.");
$e->addUserMessage("This is a custom error message that will be sent back to the client.  Hello, client!");
throw $e;
```

## Repository

`userfrosting/support` provides a generic `Repository` class that extends Laravel's base [Repository](https://laravel.com/api/5.4/Illuminate/Config/Repository.html), and on which various other UserFrosting components depend.  For example, UserFrosting's `config`, `translator`, and Fortress `schema` all store their data in a UserFrosting `Repository` or child class.

The `Repository` class provides the following methods:

### `has(string $key)`

Determine if the given configuration value exists.

### `get(string $key, mixed $default = null)`

Get the specified configuration value.

### `set(array|string $key, mixed $value = null)`

Set a given configuration value.

### `prepend(string $key, mixed $value)`

Prepend a value onto an array configuration value.

### `push(string $key, mixed $value)`

Push a value onto an array configuration value.

### `all()`

Get all of the configuration items for the application.

### `mergeItems(string $key = null, mixed $items)`

Merge a value or array of values into the repository using `array_replace_recursive` at the chosen key.  If the `$key` is null, it will merge into the entire repository.

## Loaders

Loader classes allow you to load repository data from multiple sources and merge them into a common data structure.  The abstract class `FileRepositoryLoader` manages an ordered list of file paths.  When the `load` method is called on a concrete implementation of `FileRepositoryLoader`, it will use the implementation of `parseFile` to read the contents of each file and merge them together, returning an array of the merged contents.  The `load` method uses the [`array_replace_recursive` function](http://php.net/manual/en/function.array-replace-recursive.php) to perform this merge.

As an example, consider the `YamlFileLoader` implementation that loads two schema files:

```yaml
# core/schema/contact.yaml

name:
    validators:
        length:
            min: 1
            max: 200
            message: Please enter a name between 1 and 200 characters.
        required :
            message : Please specify your name.

email:
    validators:
        length:
            min: 1
            max: 150
            message: Please enter an email address between 1 and 150 characters.

        email:
            message : That does not appear to be a valid email address.

# account/schema/contact.yaml

email:
    validators:
        required:
            message: Please specify your email address.

message:
    validators:
        required:
            message: Please enter a message
```

To load and merge these two schema files into a Respository:

```
$loader = new \UserFrosting\Support\Repository\Loader\YamlFileLoader([
    'core/schema/contact.yaml',
    'account/schema/contact.yaml'
]);
$schema = new \UserFrosting\Support\Repository\Repository($loader->load());
```

## Path Builders

The abstract `PathBuilder` class uses an instance of the [`UniformResourceLocator`](https://github.com/userfrosting/uniformresourcelocator) to build a customized list of paths that can be passed into a `Loader` class.

For example, the `StreamPathBuilder` class takes a `UniformResourceLocator` and a [stream path](https://webmozart.io/blog/2013/06/19/the-power-of-uniform-resource-location-in-php/) that has been registered with the locator, and returns a list of matching paths when you call the `buildPaths` method:

```
$builder = new \UserFrosting\Support\Repository\PathBuilder\StreamPathBuilder($this->locator, 'owls://megascops.php');
$paths = $builder->buildPaths();

// Returns a list of paths matching owls://megascops.php:
[
    '/core/owls/megascops.php',
    '/account/owls/megascops.php',
    '/admin/owls/megascops.php'
]
```

You can define other `PathBuilder` classes to customize the way this list of paths is constructed.  Simply implement the `buildPaths` method, returning an array of generated paths in the order in which they should be loaded by a `Loader` class.

## DotenvEditor

The `DotenvEditor` class provides an implementation of [JackieDo Laravel-Dotenv-Editor package ](https://github.com/JackieDo/Laravel-Dotenv-Editor) for UserFrosting. This can be used to read and write the .env configuration file (or files with same structure and syntax).

For complete usage of `DotenvEditor` class, see [](https://github.com/JackieDo/Laravel-Dotenv-Editor#reading-file-content).

## [Style Guide](STYLE-GUIDE.md)

## [Testing](RUNNING_TESTS.md)
