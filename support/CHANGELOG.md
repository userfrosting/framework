# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [4.5.0]
- Update dependencies

## [4.4.1]
- Replaced Travis with GitHub Action for build
- Fix deprecation in tests

## [4.4.0]
- Update dependencies

## [4.3.4]
- Added PHP 7.4 to Travis
- Fix issue with test data

## [4.3.3]
- Remove '?string' in `UserFrosting\Support\Repository:mergeItems()` method to restore compatability with RequestSchemaInterface.

## [4.3.2]
- Remove ':array' in ` UserFrosting\Support\Repository\PathBuilder:buildPaths()` abstract method to restore compatability with ConfigPathBuilder

## [4.3.1]
- Improved code quality & type hinting
- Fixed issue where an empty Yaml file would produce a null. Now returns an empty array
- `UserFrosting\Support\DotenvEditor\DotenvEditor::load` will throw an error if a `null` path is passed.
- Replaced deprecated code in `UserFrosting\Support\Repository\Repository`

## [4.3.0]
- Dropping support for PHP 5.6 & 7.0
- Updated Illuminate/Config to 5.8
- Updated PHPUnit to 7.5

## [4.2.1]
- Fix issue with FileRepositoryLoader's `prependPath` method.
- Fix issue with `Util::stripPrefix` if prefix is same as string to process on PHP 5.6.
- 100% test coverage

## [4.2.0]
- Replaced `rockettheme/toolbox` with `userfrosting/uniformresourcelocator`.

## [4.1.3]
- Lower required version of symfony/yaml

## [4.1.2]
- Implement `DotenvEditor` class

## [4.1.1]
- Implement `NotFoundException`

## [4.1.0]
- Implement `Loader` and `PathBuilder` classes
- Some renaming of variables
- Unit tests...finally

## 4.0.0
- Initial Release

[4.4.1]: https://github.com/userfrosting/support/compare/4.4.0...4.4.1
[4.4.0]: https://github.com/userfrosting/support/compare/4.3.3...4.4.0
[4.3.4]: https://github.com/userfrosting/support/compare/4.3.3...4.3.4
[4.3.3]: https://github.com/userfrosting/support/compare/4.3.2...4.3.3
[4.3.2]: https://github.com/userfrosting/support/compare/4.3.1...4.3.2
[4.3.1]: https://github.com/userfrosting/support/compare/4.3.0...4.3.1
[4.3.0]: https://github.com/userfrosting/support/compare/4.2.1...4.3.0
[4.2.1]: https://github.com/userfrosting/support/compare/4.2.0...4.2.1
[4.2.0]: https://github.com/userfrosting/support/compare/4.1.3...4.2.0
[4.1.3]: https://github.com/userfrosting/support/compare/4.1.2...4.1.3
[4.1.2]: https://github.com/userfrosting/support/compare/4.1.1...4.1.2
[4.1.1]: https://github.com/userfrosting/support/compare/4.1.0...4.1.1
[4.1.0]: https://github.com/userfrosting/support/compare/4.0.0...4.1.0
