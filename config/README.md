# Config module for UserFrosting 4

[![Latest Version](https://img.shields.io/github/release/userfrosting/config.svg)](https://github.com/userfrosting/config/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Join the chat at https://chat.userfrosting.com/channel/support](https://chat.userfrosting.com/api/v1/shield.svg?name=UserFrosting)](https://chat.userfrosting.com/channel/support)
[![Donate](https://img.shields.io/badge/Open%20Collective-Donate-blue.svg)](https://opencollective.com/userfrosting#backer)

| Branch | Build | Coverage | Style |
| ------ |:-----:|:--------:|:-----:|
| [master][Config]  | [![][config-master-build]][config-travis] | [![][config-master-codecov]][config-codecov] | [![][config-style-master]][config-style] |
| [develop][config-develop] | [![][config-develop-build]][config-travis] | [![][config-develop-codecov]][config-codecov] | [![][config-style-develop]][config-style] |

<!-- Links -->
[Config]: https://github.com/userfrosting/config
[config-develop]: https://github.com/userfrosting/config/tree/develop
[config-version]: https://img.shields.io/github/release/userfrosting/config.svg
[config-master-build]: https://github.com/userfrosting/config/workflows/Build/badge.svg?branch=master
[config-master-codecov]: https://codecov.io/gh/userfrosting/config/branch/master/graph/badge.svg
[config-develop-build]: https://github.com/userfrosting/config/workflows/Build/badge.svg?branch=develop
[config-develop-codecov]: https://codecov.io/gh/userfrosting/config/branch/develop/graph/badge.svg
[config-releases]: https://github.com/userfrosting/config/releases
[config-travis]: https://github.com/userfrosting/config/actions?query=workflow%3ABuild
[config-codecov]: https://codecov.io/gh/userfrosting/config
[config-style-master]: https://github.styleci.io/repos/54955134/shield?branch=master&style=flat
[config-style-develop]: https://github.styleci.io/repos/54955134/shield?branch=develop&style=flat
[config-style]: https://github.styleci.io/repos/54955134

## Usage

Create a file `default.php`, in a directory `/path/to/core/config/`:

**default.php**

```
return [
    'contacts' => [
        'housekeeper' => [
            'name' => 'Alex',
            'email' => 'alex@cleansthetoilet.com'
        ]
    ]
];
```

Suppose now you have another config file which can override values in this base config file.  For example, in `/path/to/plugin/config/`, you have:

**default.php**

```
return [
    'contacts' => [
        'housekeeper' => [
            'name' => 'Alex "the man" Weissman'
        ]
    ]
];
```

You can generate an ordered list of these configuration files using the `ConfigPathBuilder` class, and merge them together using an instance of `UserFrosting\Support\Respository\Loader\ArrayFileLoader`.

### Path builder

Create `ResourceLocator` and `ConfigPathBuilder` classes to build a list of configuration files:

```
$locator = new ResourceLocator(__DIR__);
$locator->registerLocation('core', 'path/to/core');
$locator->registerLocation('plugin', 'path/to/plugin');
$locator->registerStream('config', '', 'config/');

$builder = new ConfigPathBuilder($locator, 'config://');
$paths = $builder->buildPaths();

// Generates a list of paths:
[
    '/core/config/default.php'
    '/plugin/config/default.php'
]
```

### Data loader

You can then use the `ArrayFileLoader` class to load and merge all configuration data from this list of paths:

```
$loader = new \UserFrosting\Support\Respository\Loader\ArrayFileLoader($builder->buildPaths());
$config = new \UserFrosting\Support\Respository\Repository($loader->load());
```

Config files in multiple paths will be merged in the order in which the paths are specified.  You can now access your configuration data via the standard `Repository` methods:

```
echo $config->get('contacts.housekeeper.name');
// Prints 'Alex'
```

You can also specify environment-specific config files in each path.  If an environment name is passed to `buildPaths()`, `ConfigPathBuilder` will merge in the environment-specific file in a path immediately after merging in `default.php`:

**development.php**

```
return [
    'database' => [
        'password' => 'sup3rC-cr3t'
    ]
];
```

To merge this in, you would call:

```
$paths = $builder->buildPaths('development');
```

## [Style Guide](STYLE-GUIDE.md)

## [Testing](RUNNING_TESTS.md)
