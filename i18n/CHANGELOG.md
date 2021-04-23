# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [4.5.0]
- Added Windows OS to CI tests
- Updated dependencies
- Improved testing for edge cases

## [4.4.3]
- Replaced Travis with GitHub Action for build
- Upgrade deprecation in tests

## [4.4.2]
- Fixed issue where locale files from higher priority locations would not overwrite files from lower priority locations. (N.B.: This fix means the files withing the same locations will be loaded in BACKWARD order. Having the location order is more important in this case.)

## [4.4.1]
- Throws an exception if `translate` placeholder are not numeric or array.
- Fix issue with numeric placeholder with non plural keys. See [userfrosting#1090](https://github.com/userfrosting/UserFrosting/issues/1090#issuecomment-620832985).

## [4.4.0]
Complete rewrite of the Translator.

Instead of `LocalePathBuilder -> MessageTranslator`, the new translator introduce the concept of `Locale`, `Dictionary` and `Translator`. So now you create a locale, which enables you to create a dictionary for that locale, which finally allows you to create a translator which will used that dictionary.

Instead of loading multiple locale on top of each other, a locale can depend on other locale using the configuration file (ie. `locale.yaml`). See the updated doc for more information.

Plural rule are now defined in the locale configuration file instead of the special `@PLURAL_RULE` key.

All methods of the `Translator` are the same for backward compatibility. The only change is the constructor, which now requires a `DictionaryInterface` instance.

**Detailed changes** :
- `MessageTranslator` is now `Translator`.
- `Translator` requires a `DictionaryInterface` as some constructor argument instead of paths.
- `LocalePathBuilder` removed.
- `DictionaryInterface` now extends `UserFrosting\Support\Repository\Repository` instead of the `Translator`. The raw data can be accessed using the Dictionary methods.
- `@PLURAL_RULE` special key removed. Use the Locale configuration file (`locale.yaml`) `plural_rule` attribute instead.
- Translator can't load multiple locale anymore. Use the Locale configuration file `parents` attribute instead.

See updated [documentation](README.md) for more details on how to use the new Translator, Locale and Dictionary.

## [4.3.0]
- Dropping support for PHP 5.6 & 7.0
- Updated Twig to 2.x
- Updated PHPUnit to 7.5

## [4.2.1]
- 100% Test coverage ([#9])
- Factor translate() into smaller methods ([#3])
- Fix misc issues for edge cases, especially when a plural rule couldn't be found
- General code improvements, test optimizations and code styling

## [4.2.0]
- Replaced `rockettheme/toolbox` with `userfrosting/uniformresourcelocator` from `userfrosting/support` **4.2.0**.

## [4.1.0]
- `MessageTranslator` is now an extension of the base `Repository` class in userfrosting/support.
- Factored out the path building methods into `LocalePathBuilder`.
- Removed loading methods.  We now rely on the `ArrayFileLoader` class in userfrosting/support.

## [4.0.3]
- Fixed Illuminate/config version requirement

## [4.0.2]
- Moved dependencies to `require`
- Fixed standards compliance issues

## [4.0.1]
- Replacing `str_replace` with Twig for placeholder replacement

## 4.0.0
- Initial release

[4.5.0]: https://github.com/userfrosting/i18n/compare/4.4.3...4.5.0
[4.4.3]: https://github.com/userfrosting/i18n/compare/4.4.2...4.4.3
[4.4.2]: https://github.com/userfrosting/i18n/compare/4.4.1...4.4.2
[4.4.1]: https://github.com/userfrosting/i18n/compare/4.4.0...4.4.1
[4.4.0]: https://github.com/userfrosting/i18n/compare/4.3.0...4.4.0
[4.3.0]: https://github.com/userfrosting/i18n/compare/4.2.1...4.3.0
[4.2.1]: https://github.com/userfrosting/i18n/compare/4.2.0...4.2.1
[4.2.0]: https://github.com/userfrosting/i18n/compare/4.1.0...4.2.0
[4.1.0]: https://github.com/userfrosting/i18n/compare/4.0.3...4.1.0
[4.0.3]: https://github.com/userfrosting/i18n/compare/4.0.2...4.0.3
[4.0.2]: https://github.com/userfrosting/i18n/compare/4.0.1...4.0.2
[4.0.1]: https://github.com/userfrosting/i18n/compare/4.0.0...4.0.1
[#3]: https://github.com/userfrosting/i18n/issues/3
[#9]: https://github.com/userfrosting/i18n/issues/9
