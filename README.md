# Session

[![Release](https://img.shields.io/packagist/v/icanboogie/session.svg)](https://packagist.org/packages/icanboogie/session)
[![Build Status](https://img.shields.io/travis/ICanBoogie/Session/master.svg)](http://travis-ci.org/ICanBoogie/Session)
[![HHVM](https://img.shields.io/hhvm/icanboogie/session.svg)](http://hhvm.h4cc.de/package/icanboogie/session)
[![Code Quality](https://img.shields.io/scrutinizer/g/ICanBoogie/Session/master.svg)](https://scrutinizer-ci.com/g/ICanBoogie/Session)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/Session/master.svg)](https://coveralls.io/r/ICanBoogie/Session)
[![Packagist](https://img.shields.io/packagist/dt/icanboogie/session.svg)](https://packagist.org/packages/icanboogie/session)

The **icanboogie/session** package provides an interface to easily manage PHP sessions. You create a session instance with the desired options and the session is automatically started when reading or writing. The session instance is used as an array, just like `$_SESSION`. You can provide _session segments_ to your components so that they have a safe place to store their own session values. Finally, you can use the _session token_ with unsafe HTTP methods to prevent [CSRF][].

It is important to keep in mind that the session instance it basically mapping the `$_SESSION` array and `session_*` functions, thus you don't need to change anything in your application setup. You may use Redis to store sessions and some other fancy session handler, it makes no difference.

The following code demonstrates some usages of the session instance:

```php
<?php

use ICanBoogie\Session;

$session = new Session;

#
# The session is automatically started when reading or writing
#
if (!isset($session['bar']))
{
	$session['bar'] = 'foo';
}

#
# Optionally, changes can be commited right away,
# also closing the session.
#
$session->commit();

#
# The session is automatically started again
#
$session['baz'] = 'dib';

#
# Using isolated session segments
#
$segment = $session->fragments['Vendor\NameSpace'];
$segment['bar'] = 123;
echo $session->fragments['Vendor\NameSpace']['bar']; // 123
$session->fragments['Vendor\NameSpace']['bar'] = 456;
echo $segment['bar']; // 456

#
# The session token can be used to prevent CSRF. A new token is
# generated if none exists.
#
$token = $session->token;       // 86eac2e0d91521df1efe7422e0d9ce48ef5ac75778ca36176bf4b84db9ff35858e491923e6f034ece8bcc4b38c3f7e99
$session->verify_token($token); // true

#
# Of course all of this is just mapping to the `$_SESSION` array
#
echo $_SESSION['bar']; // foo
echo $_SESSION['baz']; // dib
echo $_SESSION['Vendor\NameSpace']['bar']; // 456
```





## Getting started

A [Session][] instance is a representation of a PHP session. It is created with parameters mapped to `session_*` functions. Options can be defined to customize you session, their default values are inherited from the PHP config.

The following code demonstrates how a session using default values can be instantiated:

```php
use ICanBoogie\Session;

$session = new Session;
```

The following code demonstrates how options can be used to customize the session instance. Only a few options are demonstrated here, more are available.

```php
use ICanBoogie\Session;
use ICanBoogie\Session\CookieParams;

$session = new Session([

	Session::OPTION_NAME => 'SID',
	Session::OPTION_CACHE_LIMITER => 'public',
	Session::OPTION_COOKIE_PARAMS => [
	
		CookieParams::OPTION_DOMAIN => '.mydomain.tld',
		CookieParams::OPTION_SECURE => true
	
	]

]);
```

If you are defining these options in a config file, you might want to use the light weight `SessionOptions` interface:

```php
<?php

// config/session.php

use ICanBoogie\SessionOptions as Session;

return [

	Session::OPTION_NAME => 'SID',
	Session::OPTION_CACHE_LIMITER => 'public',
	Session::OPTION_COOKIE_PARAMS => [
	
		CookieParams::OPTION_DOMAIN => '.mydomain.tld',
		CookieParams::OPTION_SECURE => true
	
	]

];
```







----------





## Requirements

The package requires PHP 5.5 or later.





## Installation

The recommended way to install this package is through [Composer](http://getcomposer.org/):

```
$ composer require icanboogie/session
```





### Cloning the repository

The package is [available on GitHub](https://github.com/ICanBoogie/Session), its repository can be cloned with the following command line:

	$ git clone https://github.com/ICanBoogie/Session.git





## Documentation

The package is documented as part of the [ICanBoogie][] framework
[documentation][]. You can generate the documentation for the package and its dependencies with the `make doc` command. The documentation is generated in the `build/docs` directory. [ApiGen](http://apigen.org/) is required. The directory can later be cleaned with the `make clean` command.





## Testing

The test suite is ran with the `make test` command. [PHPUnit](https://phpunit.de/) and [Composer](http://getcomposer.org/) need to be globally available to run the suite. The command installs dependencies as required. The `make test-coverage` command runs test suite and also creates an HTML coverage report in `build/coverage`. The directory can later be cleaned with the `make clean` command.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://img.shields.io/travis/ICanBoogie/Session/master.svg)](http://travis-ci.org/ICanBoogie/Session)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/Session/master.svg)](https://coveralls.io/r/ICanBoogie/Session)





## License

**icanboogie/session** is licensed under the New BSD License - See the [LICENSE](LICENSE) file for details.





[Session]:    http://api.icanboogie.org/session/latest/class-ICanBoogie.Session.html
[ICanBoogie]: https://github.com/ICanBoogie/ICanBoogie
[CSRF]:       https://en.wikipedia.org/wiki/Cross-site_request_forgery
