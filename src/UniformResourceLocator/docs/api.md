## Table of contents

- [Table of contents](#table-of-contents)
  - [Class: \UserFrosting\UniformResourceLocator\ResourceLocation](#class-userfrostinguniformresourcelocatorresourcelocation)
  - [Class: \UserFrosting\UniformResourceLocator\Normalizer](#class-userfrostinguniformresourcelocatornormalizer)
  - [Interface: \UserFrosting\UniformResourceLocator\ResourceLocationInterface](#interface-userfrostinguniformresourcelocatorresourcelocationinterface)
  - [Class: \UserFrosting\UniformResourceLocator\ResourceStream](#class-userfrostinguniformresourcelocatorresourcestream)
  - [Interface: \UserFrosting\UniformResourceLocator\ResourceLocatorInterface](#interface-userfrostinguniformresourcelocatorresourcelocatorinterface)
  - [Interface: \UserFrosting\UniformResourceLocator\ResourceInterface](#interface-userfrostinguniformresourcelocatorresourceinterface)
  - [Class: \UserFrosting\UniformResourceLocator\Resource](#class-userfrostinguniformresourcelocatorresource)
  - [Class: \UserFrosting\UniformResourceLocator\ResourceLocator](#class-userfrostinguniformresourcelocatorresourcelocator)
  - [Interface: \UserFrosting\UniformResourceLocator\ResourceStreamInterface](#interface-userfrostinguniformresourcelocatorresourcestreaminterface)
  - [Class: \UserFrosting\UniformResourceLocator\Exception\StreamNotFoundException](#class-userfrostinguniformresourcelocatorexceptionstreamnotfoundexception)
  - [Class: \UserFrosting\UniformResourceLocator\Exception\LocationNotFoundException](#class-userfrostinguniformresourcelocatorexceptionlocationnotfoundexception)

<hr />

### Class: \UserFrosting\UniformResourceLocator\ResourceLocation

> ResourceLocation Class. The representation of a location

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>\string</em> <strong>$name</strong>, <em>\string</em> <strong>$path=null</strong>)</strong> : <em>void</em><br /><em>Constructor.</em> |
| public | <strong>getName()</strong> : <em>string</em> |
| public | <strong>getPath()</strong> : <em>string</em> |
| public | <strong>setName(</strong><em>\string</em> <strong>$name</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em> |
| public | <strong>setPath(</strong><em>\string</em> <strong>$path</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em> |

*This class implements [\UserFrosting\UniformResourceLocator\ResourceLocationInterface](#interface-userfrostinguniformresourcelocatorresourcelocationinterface)*

<hr />

### Class: \UserFrosting\UniformResourceLocator\Normalizer

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>normalize(</strong><em>string</em> <strong>$uri</strong>, <em>bool</em> <strong>$throwException=false</strong>, <em>bool</em> <strong>$splitStream=false</strong>)</strong> : <em>string/array/bool Return false if path is invalid</em><br /><em>Returns the canonicalized URI on success. The resulting path will have no '/./' or '/../' components. Trailing delimiter `/` is kept. Can also split the `scheme` for the `path` part of the uri if $splitStream parameter is set to true By default (if $throwException parameter is not set to true) returns false on failure.</em> |
| public static | <strong>normalizePath(</strong><em>\string</em> <strong>$path</strong>)</strong> : <em>string/false Return false if path is invalid</em><br /><em>Normalise a path: - Make sure all `\` (from a Windows path) are changed to `/` - Make sure a trailling slash is present - Doesn't change the beginning of the path (don't change absolute / relative path), but will change `C:\` to `C:/`.</em> |

<hr />

### Interface: \UserFrosting\UniformResourceLocator\ResourceLocationInterface

> ResourceLocationInterface Interface.

| Visibility | Function |
|:-----------|:---------|
| public | <strong>getName()</strong> : <em>string</em> |
| public | <strong>getPath()</strong> : <em>string</em> |
| public | <strong>setName(</strong><em>\string</em> <strong>$name</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em> |
| public | <strong>setPath(</strong><em>\string</em> <strong>$path</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em> |

<hr />

### Class: \UserFrosting\UniformResourceLocator\ResourceStream

> ResourceStream Class. The representation of a stream

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>string</em> <strong>$scheme</strong>, <em>string</em> <strong>$prefix=`''`</strong>, <em>string</em> <strong>$path=null</strong>, <em>bool</em> <strong>$shared=false</strong>)</strong> : <em>void</em><br /><em>Constructor.</em> |
| public | <strong>getPath()</strong> : <em>string</em> |
| public | <strong>getPrefix()</strong> : <em>string</em> |
| public | <strong>getScheme()</strong> : <em>string</em> |
| public | <strong>isShared()</strong> : <em>bool</em> |
| public | <strong>setPath(</strong><em>string</em> <strong>$path</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em> |
| public | <strong>setPrefix(</strong><em>string</em> <strong>$prefix</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em> |
| public | <strong>setScheme(</strong><em>string</em> <strong>$scheme</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em> |
| public | <strong>setShared(</strong><em>bool</em> <strong>$shared</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em> |

*This class implements [\UserFrosting\UniformResourceLocator\ResourceStreamInterface](#interface-userfrostinguniformresourcelocatorresourcestreaminterface)*

<hr />

### Interface: \UserFrosting\UniformResourceLocator\ResourceLocatorInterface

> ResourceLocatorInterface Class.

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__invoke(</strong><em>string</em> <strong>$uri</strong>)</strong> : <em>string/bool</em> |
| public | <strong>addLocation(</strong><em>[\UserFrosting\UniformResourceLocator\ResourceLocationInterface](#interface-userfrostinguniformresourcelocatorresourcelocationinterface)</em> <strong>$location</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em><br /><em>Add an existing RessourceLocation instance to the location list.</em> |
| public | <strong>addStream(</strong><em>[\UserFrosting\UniformResourceLocator\ResourceStreamInterface](#interface-userfrostinguniformresourcelocatorresourcestreaminterface)</em> <strong>$stream</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em><br /><em>Add an exisitng ResourceStream to the stream list.</em> |
| public | <strong>getBasePath()</strong> : <em>string</em> |
| public | <strong>getLocation(</strong><em>\string</em> <strong>$name</strong>)</strong> : <em>[\UserFrosting\UniformResourceLocator\ResourceLocationInterface](#interface-userfrostinguniformresourcelocatorresourcelocationinterface)</em><br /><em>Get a location instance based on it's name.</em> |
| public | <strong>getLocations()</strong> : <em>[\UserFrosting\UniformResourceLocator\ResourceLocationInterface](#interface-userfrostinguniformresourcelocatorresourcelocationinterface)[]</em><br /><em>Get a a list of all registered locations.</em> |
| public | <strong>getResource(</strong><em>\string</em> <strong>$uri</strong>, <em>\bool</em> <strong>$first=false</strong>)</strong> : <em>[\UserFrosting\UniformResourceLocator\ResourceInterface](#interface-userfrostinguniformresourcelocatorresourceinterface)/bool Returns false if resource is not found</em><br /><em>Return a resource instance.</em> |
| public | <strong>getResources(</strong><em>\string</em> <strong>$uri</strong>, <em>\bool</em> <strong>$all=false</strong>)</strong> : <em>ResourceInterface[] Array of Resources</em><br /><em>Return a list of resources instances.</em> |
| public | <strong>getStream(</strong><em>\string</em> <strong>$scheme</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\array<string,array<ResourceStreamInterface>></em><br /><em>Return information about a specfic stream. Return value is an array of ResourceStreamInterface, for each prefix For example : $array = array( ''       => ResourceStreamInterface, 'prefix' => ResourceStreamInterface );.</em> |
| public | <strong>getStreams()</strong> : <em>\UserFrosting\UniformResourceLocator\array<string,array<string,array<ResourceStreamInterface>>></em><br /><em>Return information about a all registered stream. Return value is an array of array of ResourceStreamInterface, for each prefix For example : 'bar' => array( ''       => ResourceStreamInterface, 'prefix' => ResourceStreamInterface ), 'foo' => array( ''       => ResourceStreamInterface, 'blah'   => ResourceStreamInterface );.</em> |
| public | <strong>listLocations()</strong> : <em>string[] An array of registered name => location</em><br /><em>Return a list of all the locations registered by name.</em> |
| public | <strong>listResources(</strong><em>\string</em> <strong>$uri</strong>, <em>\bool</em> <strong>$all=false</strong>, <em>\bool</em> <strong>$sort=true</strong>)</strong> : <em>ResourceInterface[] The ressources list</em><br /><em>List all ressources found at a given uri. Same as listing all file in a directory, except here all topmost ressources will be returned when considering all locations.</em> |
| public | <strong>listStreams()</strong> : <em>string[] An array of registered scheme => location</em><br /><em>Return a list of all the stream scheme registered.</em> |
| public | <strong>locationExist(</strong><em>\string</em> <strong>$name</strong>)</strong> : <em>bool</em><br /><em>Returns true if a location has been defined.</em> |
| public | <strong>registerLocation(</strong><em>\string</em> <strong>$name</strong>, <em>\string</em> <strong>$path=null</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em><br /><em>Register a new location.</em> |
| public | <strong>registerStream(</strong><em>\string</em> <strong>$scheme</strong>, <em>\string</em> <strong>$prefix=`''`</strong>, <em>string/string[]/null</em> <strong>$paths=null</strong>, <em>\bool</em> <strong>$shared=false</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em><br /><em>Register a new stream.</em> |
| public | <strong>removeLocation(</strong><em>\string</em> <strong>$name</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em><br /><em>Unregister the specified location.</em> |
| public | <strong>removeStream(</strong><em>\string</em> <strong>$scheme</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em><br /><em>Unregister the specified stream.</em> |
| public | <strong>schemeExists(</strong><em>\string</em> <strong>$scheme</strong>)</strong> : <em>bool</em><br /><em>Returns true if a stream has been defined.</em> |

*This class implements \RocketTheme\Toolbox\ResourceLocator\ResourceLocatorInterface*

<hr />

### Interface: \UserFrosting\UniformResourceLocator\ResourceInterface

> Resource Interface.

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__toString()</strong> : <em>string The resource absolute path</em><br /><em>Magic function to convert the class into the resource absolute path.</em> |
| public | <strong>getAbsolutePath()</strong> : <em>string</em> |
| public | <strong>getBasePath()</strong> : <em>string</em><br /><em>Get the resource base path, aka the path that comes after the `://`.</em> |
| public | <strong>getBasename()</strong> : <em>string</em><br /><em>Extract the trailing name component (test.txt -> test.txt).</em> |
| public | <strong>getExtension()</strong> : <em>string</em><br /><em>Extract the resource extension (test.txt -> txt).</em> |
| public | <strong>getFilename()</strong> : <em>string</em><br /><em>Extract the resource filename (test.txt -> test).</em> |
| public | <strong>getLocation()</strong> : <em>[\UserFrosting\UniformResourceLocator\ResourceLocationInterface](#interface-userfrostinguniformresourcelocatorresourcelocationinterface)/null</em> |
| public | <strong>getLocatorBasePath()</strong> : <em>string</em> |
| public | <strong>getPath()</strong> : <em>string</em> |
| public | <strong>getStream()</strong> : <em>[\UserFrosting\UniformResourceLocator\ResourceStreamInterface](#interface-userfrostinguniformresourcelocatorresourcestreaminterface)</em> |
| public | <strong>getUri()</strong> : <em>string</em><br /><em>Get Resource URI.</em> |

<hr />

### Class: \UserFrosting\UniformResourceLocator\Resource

> Resource Class. Resources are used to represent a file with info regarding the stream and Location used to find it. When a resource is created, we save the stream used to find it, the location where it was found, and the absolute and relative paths of the file. Using this information, we can later rebuilt the URI used to find this file. Since the full path will contains the relative location of the stream and location inside the filesystem, this information will be removed to recrete the relative 'basepath' of the file, allowing the recreatation of the uri (scheme://basePath).

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>[\UserFrosting\UniformResourceLocator\ResourceStreamInterface](#interface-userfrostinguniformresourcelocatorresourcestreaminterface)</em> <strong>$stream</strong>, <em>[\UserFrosting\UniformResourceLocator\ResourceLocationInterface](#interface-userfrostinguniformresourcelocatorresourcelocationinterface)/null/[\UserFrosting\UniformResourceLocator\ResourceLocationInterface](#interface-userfrostinguniformresourcelocatorresourcelocationinterface)</em> <strong>$location=null</strong>, <em>\string</em> <strong>$path</strong>, <em>\string</em> <strong>$locatorBasePath=`''`</strong>)</strong> : <em>void</em><br /><em>Constructor.</em> |
| public | <strong>__toString()</strong> : <em>string The resource absolute path</em><br /><em>Magic function to convert the class into the resource absolute path.</em> |
| public | <strong>getAbsolutePath()</strong> : <em>string</em> |
| public | <strong>getBasePath()</strong> : <em>string</em><br /><em>Get the resource base path, aka the path that comes after the `://`. To to this, we use the relative path and remove the stream and location base path. For example, a stream with a base path of `data/foo/`, will return a relative path for every resource it find as `data/foo/filename.txt`. So we want to remove the `data/foo/` part to keep only the `filename.txt` part, aka the part after the `://` in the URI. Same goes for the location part, which comes before the stream: `locations/locationA/data/foo`</em> |
| public | <strong>getBasename()</strong> : <em>string</em><br /><em>Extract the trailing name component (test.txt -> test.txt).</em> |
| public | <strong>getExtension()</strong> : <em>string</em><br /><em>Extract the resource extension (test.txt -> txt).</em> |
| public | <strong>getFilename()</strong> : <em>string</em><br /><em>Extract the resource filename (test.txt -> test).</em> |
| public | <strong>getLocation()</strong> : <em>[\UserFrosting\UniformResourceLocator\ResourceLocationInterface](#interface-userfrostinguniformresourcelocatorresourcelocationinterface)/null</em> |
| public | <strong>getLocatorBasePath()</strong> : <em>string</em> |
| public | <strong>getPath()</strong> : <em>string</em> |
| public | <strong>getStream()</strong> : <em>[\UserFrosting\UniformResourceLocator\ResourceStreamInterface](#interface-userfrostinguniformresourcelocatorresourcestreaminterface)</em> |
| public | <strong>getUri()</strong> : <em>string</em><br /><em>Get Resource URI Also adds the prefix stream prefix if it existprefix.</em> |
| public | <strong>setLocation(</strong><em>[\UserFrosting\UniformResourceLocator\ResourceLocationInterface](#interface-userfrostinguniformresourcelocatorresourcelocationinterface)/null/[\UserFrosting\UniformResourceLocator\ResourceLocationInterface](#interface-userfrostinguniformresourcelocatorresourcelocationinterface)</em> <strong>$location</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\self</em><br /><em>Set the value of location.</em> |
| public | <strong>setLocatorBasePath(</strong><em>\string</em> <strong>$locatorBasePath</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em> |
| public | <strong>setPath(</strong><em>\string</em> <strong>$path</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\self</em><br /><em>Set relative path to the resource, above the locator base path.</em> |
| public | <strong>setStream(</strong><em>[\UserFrosting\UniformResourceLocator\ResourceStreamInterface](#interface-userfrostinguniformresourcelocatorresourcestreaminterface)</em> <strong>$stream</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\self</em><br /><em>Set the value of stream.</em> |

*This class implements [\UserFrosting\UniformResourceLocator\ResourceInterface](#interface-userfrostinguniformresourcelocatorresourceinterface)*

<hr />

### Class: \UserFrosting\UniformResourceLocator\ResourceLocator

> ResourceLocator Class. The locator is used to find resources.

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>\string</em> <strong>$basePath=`''`</strong>)</strong> : <em>void</em><br /><em>Constructor.</em> |
| public | <strong>__invoke(</strong><em>mixed</em> <strong>$uri</strong>)</strong> : <em>void</em> |
| public | <strong>addLocation(</strong><em>[\UserFrosting\UniformResourceLocator\ResourceLocationInterface](#interface-userfrostinguniformresourcelocatorresourcelocationinterface)</em> <strong>$location</strong>)</strong> : <em>void</em> |
| public | <strong>addPath(</strong><em>\string</em> <strong>$scheme</strong>, <em>\string</em> <strong>$prefix</strong>, <em>string/string[]</em> <strong>$paths</strong>, <em>bool/bool/string</em> <strong>$override=false</strong>, <em>\bool</em> <strong>$force=false</strong>)</strong> : <em>void</em><br /><em>AddPath function. Used to preserve compatibility with RocketTheme/Toolbox.</em> |
| public | <strong>addStream(</strong><em>[\UserFrosting\UniformResourceLocator\ResourceStreamInterface](#interface-userfrostinguniformresourcelocatorresourcestreaminterface)</em> <strong>$stream</strong>)</strong> : <em>void</em> |
| public | <strong>findResource(</strong><em>string</em> <strong>$uri</strong>, <em>bool</em> <strong>$absolute=true</strong>, <em>bool</em> <strong>$first=false</strong>)</strong> : <em>string/bool The ressource path, or false if not found resource</em><br /><em>Find highest priority instance from a resource. Return the path for said resource For example, if looking for a `test.json` ressource, only the top priority instance of `test.json` found will be returned.</em> |
| public | <strong>findResources(</strong><em>string</em> <strong>$uri</strong>, <em>bool</em> <strong>$absolute=true</strong>, <em>bool</em> <strong>$all=false</strong>)</strong> : <em>string[] An array of all the ressources path</em><br /><em>Find all instances from a resource. Return an array of paths for said resource For example, if looking for a `test.json` ressource, all instance of `test.json` found will be listed.</em> |
| public | <strong>getBasePath()</strong> : <em>mixed</em> |
| public | <strong>getLocation(</strong><em>\string</em> <strong>$name</strong>)</strong> : <em>mixed</em> |
| public | <strong>getLocations()</strong> : <em>mixed</em> |
| public | <strong>getResource(</strong><em>\string</em> <strong>$uri</strong>, <em>\bool</em> <strong>$first=false</strong>)</strong> : <em>mixed</em> |
| public | <strong>getResources(</strong><em>\string</em> <strong>$uri</strong>, <em>\bool</em> <strong>$all=false</strong>)</strong> : <em>mixed</em> |
| public | <strong>getStream(</strong><em>\string</em> <strong>$scheme</strong>)</strong> : <em>mixed</em> |
| public | <strong>getStreamBuilder()</strong> : <em>\RocketTheme\Toolbox\StreamWrapper\StreamBuilder</em> |
| public | <strong>getStreams()</strong> : <em>mixed</em> |
| public | <strong>isStream(</strong><em>string</em> <strong>$uri</strong>)</strong> : <em>bool True if is resolvable</em><br /><em>Returns true if uri is resolvable by using locator.</em> |
| public | <strong>listLocations()</strong> : <em>void</em> |
| public | <strong>listResources(</strong><em>\string</em> <strong>$uri</strong>, <em>\bool</em> <strong>$all=false</strong>, <em>\bool</em> <strong>$sort=true</strong>)</strong> : <em>void</em> |
| public | <strong>listStreams()</strong> : <em>void</em> |
| public | <strong>locationExist(</strong><em>\string</em> <strong>$name</strong>)</strong> : <em>void</em> |
| public | <strong>registerLocation(</strong><em>\string</em> <strong>$name</strong>, <em>\string</em> <strong>$path=null</strong>)</strong> : <em>void</em> |
| public | <strong>registerSharedStream(</strong><em>\string</em> <strong>$scheme</strong>, <em>\string</em> <strong>$prefix=`''`</strong>, <em>string/string[]/null</em> <strong>$paths=null</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em><br /><em>Register a new shared stream. Shortcut for registerStream with $shared flag set to true.</em> |
| public | <strong>registerStream(</strong><em>\string</em> <strong>$scheme</strong>, <em>\string</em> <strong>$prefix=`''`</strong>, <em>mixed</em> <strong>$paths=null</strong>, <em>\bool</em> <strong>$shared=false</strong>)</strong> : <em>void</em> |
| public | <strong>removeLocation(</strong><em>\string</em> <strong>$name</strong>)</strong> : <em>void</em> |
| public | <strong>removeStream(</strong><em>\string</em> <strong>$scheme</strong>)</strong> : <em>void</em> |
| public | <strong>reset()</strong> : <em>\UserFrosting\UniformResourceLocator\static</em><br /><em>Reset locator by removing all the registered streams and locations.</em> |
| public | <strong>schemeExists(</strong><em>\string</em> <strong>$scheme</strong>)</strong> : <em>void</em> |
| public | <strong>setBasePath(</strong><em>\string</em> <strong>$basePath</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em> |
| protected | <strong>find(</strong><em>\string</em> <strong>$scheme</strong>, <em>\string</em> <strong>$file</strong>, <em>\bool</em> <strong>$array</strong>, <em>\bool</em> <strong>$all</strong>)</strong> : <em>[\UserFrosting\UniformResourceLocator\ResourceInterface](#interface-userfrostinguniformresourcelocatorresourceinterface)/[\UserFrosting\UniformResourceLocator\ResourceInterface](#interface-userfrostinguniformresourcelocatorresourceinterface)[]</em><br /><em>Returns path of a file (or directory) based on a search uri.</em> |
| protected | <strong>findCached(</strong><em>\string</em> <strong>$uri</strong>, <em>\bool</em> <strong>$array</strong>, <em>\bool</em> <strong>$all</strong>)</strong> : <em>[\UserFrosting\UniformResourceLocator\ResourceInterface](#interface-userfrostinguniformresourcelocatorresourceinterface)[]/[\UserFrosting\UniformResourceLocator\ResourceInterface](#interface-userfrostinguniformresourcelocatorresourceinterface)/false The ressource path or an array of all the ressources path or false if not resource can be found</em><br /><em>Find a resource from the cached properties.</em> |
| protected | <strong>searchPaths(</strong><em>[\UserFrosting\UniformResourceLocator\ResourceStreamInterface](#interface-userfrostinguniformresourcelocatorresourcestreaminterface)</em> <strong>$stream</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\array<string,ResourceLocationInterface/null> The search paths based on this stream and all available locations</em><br /><em>Build the search path out of the defined strean and locations. If the scheme is shared, we don't need to involve locations and can return it's path directly.</em> |
| protected | <strong>setupStreamWrapper(</strong><em>\string</em> <strong>$scheme</strong>)</strong> : <em>void</em><br /><em>Register the scheme as a php stream wrapper.</em> |
| protected | <strong>unsetStreamWrapper(</strong><em>\string</em> <strong>$scheme</strong>)</strong> : <em>void</em><br /><em>Unset a php stream wrapper.</em> |

*This class implements [\UserFrosting\UniformResourceLocator\ResourceLocatorInterface](#interface-userfrostinguniformresourcelocatorresourcelocatorinterface), \RocketTheme\Toolbox\ResourceLocator\ResourceLocatorInterface*

<hr />

### Interface: \UserFrosting\UniformResourceLocator\ResourceStreamInterface

> ResourceStreamInterface Interface.

| Visibility | Function |
|:-----------|:---------|
| public | <strong>getPath()</strong> : <em>string</em> |
| public | <strong>getPrefix()</strong> : <em>string</em> |
| public | <strong>getScheme()</strong> : <em>string</em> |
| public | <strong>isShared()</strong> : <em>bool</em> |
| public | <strong>setPath(</strong><em>string</em> <strong>$path</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em> |
| public | <strong>setPrefix(</strong><em>string</em> <strong>$prefix</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em> |
| public | <strong>setScheme(</strong><em>string</em> <strong>$scheme</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em> |
| public | <strong>setShared(</strong><em>bool</em> <strong>$shared</strong>)</strong> : <em>\UserFrosting\UniformResourceLocator\static</em> |

<hr />

### Class: \UserFrosting\UniformResourceLocator\Exception\StreamNotFoundException

> StreamNotFoundException Used when a path is not registered.

| Visibility | Function |
|:-----------|:---------|

*This class extends \Exception*

*This class implements \Throwable*

<hr />

### Class: \UserFrosting\UniformResourceLocator\Exception\LocationNotFoundException

> LocationNotFoundException Used when a Location is not registered.

| Visibility | Function |
|:-----------|:---------|

*This class extends \Exception*

*This class implements \Throwable*

