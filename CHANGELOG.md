# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [5.1.0](https://github.com/userfrosting/framework/compare/5.0.0...5.1.0)
- Drop PHP 8.1 support, add PHP 8.3 support
- Removed Assets
- Update to Laravel 10
- Update to PHPUnit 10
- [Config] Methods `getBool`, `getString`, `getInt` & `getArray` now return `null` if key doesn't exist, to make it on par with parent `get` method.

### Alert
- Messages are now translated at read time ([#1156](https://github.com/userfrosting/UserFrosting/pull/1156), [#811](https://github.com/userfrosting/UserFrosting/issues/811)). Messages will be translated when using `messages` and `getAndClearMessages`. `addMessage` now accept the optional placeholders, which will be stored with the alert message. `addMessageTranslated` is **deprecated**. 
- Translator is not optional anymore. `setTranslator` method has been removed.

## [5.0.0](https://github.com/userfrosting/framework/compare/4.6.1...5.0.0)
With version 5, this repo can be used as a bare bone Slim & Symfony Console application. It include the necessary routing class, [PHP-DI](https://php-di.org) as the Dependency Injection Container, a PSR EventDispatcher, etc. SprinkleManager has also been moved from Core/System Sprinkle and completely rewritten. 

It's necessary for the SprinkleMAnager, Slim and Symfony (Bakery) to be outside of the Core Sprinkle so it can be properly managed. All extra feature (template, database, config, etc.) are left for the Core Sprinkle. The old `sprinkle.json` has been replace with `SprinkleRecipe` interface.

Version 5 also requires PHP 8.0 and up. With that in mind, most of the code has been updated to support PHP 8 and make use of it's new features. Code quality has also been stepped up, with PHPStan analysis added to the build process.

### Global
#### Added
- Moved Alert into Framework from Core Sprinkle
- Added Bakery / Symfony Console app : `UserFrosting\Bakery\Bakery`
- UserFrosting / Slim 4 Web app : `UserFrosting\UserFrosting`
- Moved `SprinkleManager` from main repo.
- Added custom Event Dispatcher and Listeners : `UserFrosting\Event\EventDispatcher` & `UserFrosting\Event\SprinkleListenerProvider`

#### Dependencies
- Drop PHP 7.3 & 7.4 support
- Updated `twig/twig` to `^3.3`

#### Code Quality
- Updated PHPStan config and added Github Action for automatic code analysis for UniformResourceLocator & Config (with no issues on max level)
- Updated PHP-CS-Fixer & StyleCI config

### Assets
**Assets module is now deprecated and will be removed in UserFrosting 5.1 !**
#### Removed
- `UserFrosting\Assets\ServeAsset\SlimServeAsset` has been removed. Code has been moved into Core Sprinkle

### Bakery
#### Added
- `UserFrosting\Bakery\WithSymfonyStyle` moved to this repo from Core. 

### Testing
- Added helper class to test Bakery command, Container, CustomAssertionsTrait, HttpTester, TestCase, etc.

### Support
#### Removed
- These HTTP exceptions have been removed and replace with new system in Core Sprinkle :
  - `BadRequestException`
  - `ForbiddenException`
  - `HttpException`
  - `NotFoundException`
- `UserFrosting\Support\Util\Util::normalizePath` has been removed. Use `UserFrosting\UniformResourceLocator\Normalizer::normalizePath` instead.

### UniformResourceLocator
#### Changes
- Remove dependency on `rockettheme/toolbox` by integrating our own `StreamWrapper\Stream` and `StreamBuilder`
- `findResource` and `getResource` now return `null` if a resource is not found instead of `false`

#### Added
- Added `readonly` option for streams. Files accessed using a readonly scheme will be protected against destructive action at the streamwrapper level.

#### Deprecated
- `findResource` is deprecated. Use `getResource` instead
- `findResources` is deprecated. Use `getResources` instead
- `registerStream` and `registerSharedStream` are deprecated. Use `addStream` instead
- `registerLocation` is deprecated. Use `addLocation` instead

#### Removed
- Scheme Prefix has been removed
- Resource : `setLocation`, `setPath`, `setLocatorBasePath` and `setStream` methods have been removed
- ResourceLocation : `setName` and `setPath` methods have been removed
- ResourceStream : `setScheme`, `setPath` and `setShared` methods have been removed
- Deprecated `ResourceLocator::addPath` method removed
- `ResourceLocator::setBasePath` method removed

### Fortress

#### Fix
- Fix [userfrosting/UserFrosting#1216](https://github.com/userfrosting/UserFrosting/issues/1216) - Throw error when RequestSchema path doesn't exist

#### Removed
- Removed deprecated method `getSchema` in `RequestSchema`

## [4.6.1](https://github.com/userfrosting/framework/compare/4.6.0...4.6.1)
 - Fix issue with location outside of the main path not returning  the correct relative path.
 - Update php-cs-fixer to V3

## 4.6.0
 - Drop PHP 7.2 and add PHP 8.0 support.
 - Upgrade all Laravel packages to ^8.x from ^5.8.
 - Upgrade `vlucas/phpdotenv`to ^5.3 from ^3.4.
 - Upgrade `symfony/console` to ^5.1 from ^4.3.
 - Upgrade `phpunit/phpunit` to ^9.5
 - [Cache] Fix tagged file issue on Windows

## For versions before 4.6.0, see :
 - [Assets](https://github.com/userfrosting/assets/blob/master/CHANGELOG.md)
 - [Cache](https://github.com/userfrosting/cache/blob/master/CHANGELOG.md)
 - [Config](https://github.com/userfrosting/config/blob/master/CHANGELOG.md)
 - [Fortress](https://github.com/userfrosting/fortress/blob/master/CHANGELOG.md)
 - [i81n](https://github.com/userfrosting/i18n/blob/master/CHANGELOG.md)
 - [Session](https://github.com/userfrosting/session/blob/master/CHANGELOG.md)
 - [Support](https://github.com/userfrosting/support/blob/master/CHANGELOG.md)
 - [UniformResourceLocator](https://github.com/userfrosting/UniformResourceLocator/blob/master/CHANGELOG.md)