# Session

[![Release](https://img.shields.io/packagist/v/icanboogie/session.svg)](https://packagist.org/packages/icanboogie/session)
[![Build Status](https://img.shields.io/travis/ICanBoogie/Session/master.svg)](http://travis-ci.org/ICanBoogie/Session)
[![HHVM](https://img.shields.io/hhvm/icanboogie/session.svg)](http://hhvm.h4cc.de/package/icanboogie/session)
[![Code Quality](https://img.shields.io/scrutinizer/g/ICanBoogie/Session/master.svg)](https://scrutinizer-ci.com/g/ICanBoogie/Session)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/Session/master.svg)](https://coveralls.io/r/ICanBoogie/Session)
[![Packagist](https://img.shields.io/packagist/dt/icanboogie/session.svg)](https://packagist.org/packages/icanboogie/session)

The **icanboogie/session** package provides an interface to easily manage PHP sessions. You create a
session instance with the desired options and the session is automatically started when reading or
writing. The session instance is used as an array, just like `$_SESSION`. You can provide _session
segments_ to your components so that they have a safe place to store their own session values. Flash
values can be used in the session and its segments. Finally, you can use the _session token_ with
unsafe HTTP methods to prevent [CSRF][].

It is important to keep in mind that the session instance it basically mapping the `$_SESSION` array
and `session_*` functions, thus you don't need to change anything in your application setup. You may
use Redis to store sessions and some fancy session handler, it makes no difference.

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
# The session is automatically re-started on read or write
#
$session['baz'] = 'dib';

#
# Using isolated session segments
#
$segment = $session->segments['Vendor\NameSpace'];
$segment['bar'] = 123;
echo $session->segments['Vendor\NameSpace']['bar']; // 123
$session->segments['Vendor\NameSpace']['bar'] = 456;
echo $segment['bar']; // 456

#
# Using flash
#
$session->flash['info'] = "Well done!";
$session->segments['Vendor\NameSpace']->flash['bar'] = 123;

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

A [Session][] instance is a representation of a PHP session. It is created with parameters mapped to
`session_*` functions. Options can be defined to customize you session, their default values are
inherited from the PHP config.

The following code demonstrates how a session using default values can be instantiated:

```php
use ICanBoogie\Session;

$session = new Session;
```

> **Note**: Nothing prevents you from using multiple [Session][] instances but it is not recommended.

The following code demonstrates how options can be used to customize the session instance. Only a
few options are demonstrated here, more are available.

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

If you are defining these options in a config file, you might want to use the light weight
`SessionOptions` interface:

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





## Session segments

Session segments provide a safe place for components to store their values without conflicts. That
is, two components may safely use a same key because their value is stored in different session
_segments_. Segments act as namespaces for session values. It is then important to choose a safe
namespace, a class name is often the safest option.

Session and session segments instances all implement the [SessionSegment][] interface. Components
requiring session storage should use that interface rather than the [Session][] class.

> **Note**: Obtaining a segment does not start a session, only read/write may automatically start a
session. So don't hesitate to obtain session segments.

The following example demonstrates how a session segment might be injected into a controller:

```php
<?php

use ICanBoogie\SessionSegment;

class UserController
{
	/**
	 * @var SessionSegment
	 */
	private $session;
	
	public function __construct(SessionSegment $session)
	{
		$this->session = $session;
	}
	
	public function action_post_login()
	{
		// …
		
		$this->session['user_id'] = $user->id;
	}
}

// …

use ICanBoogie\Session;

$session = new Session;
$controller = new UserController($session->segments[UserControlller::class]);
```




## Flash values

Flash values are session values that are forgotten after they are read, although they can be read
multiple time during the run time of a PHP script. They can be set in the session or in its
segments.

The following example demonstrates how flash values work with the session and segments:

```php
<?php

use ICanBoogie\Session;

$session = new Session;
$session->flash['abc'] = 123;
$session->segments['segment-one']->flash['abc'] = 456;
```

The `$_SESSION` array would look like this:

```
array(2) {
  '__FLASH__' =>
  array(1) {
    'abc' =>
    int(123)
  }
  'segment-one' =>
  array(1) {
    '__FLASH__' =>
    array(1) {
      'abc' =>
      int(456)
    }
  }
}
```

After a flash values is read, it disappears from the session/segment, although it can be read
multiple time during the run time of a PHP script:

```php
<?php

$session_abc = $session->flash['abc'];   // 123
$session_abc === $session->flash['abc']; // true
$segment_abc = $session->segments['segment-one']->flash['abc'];   // 456
$segment_abc === $session->segments['segment-one']->flash['abc']; // true
```

```
array(2) {
  '__FLASH__' =>
  array(0) {
  }
  'segment-one' =>
  array(1) {
    '__FLASH__' =>
    array(0) {
    }
  }
}
```





## Defeating Cross-Site Request Forgery

The [Session][] instance provides a _session token_ that may be used to protect your application
against [Cross-Site Request Forgery][]. Your application should verify that token before processing
unsafe request, which use HTTP methods `POST`, `PUT`, and `DELETE`.

> **Note**: You can trust that the session has always a token. If none exists when a token is
requested a new one is created.

The following example demonstrates how to use the session token with a `POST` form:

```php
<?php

/**
  * @var \ICanBoogie\Session $session
  */

?>

<form method="POST" action="/articles">
	<input type="hidden" value="<?= $session->token ?>" name="_session_token />
	<!-- the remainder of the form … -->
</form>
```

When processing an unsafe request, make sure that the session token is valid:

```php
<?php

/**
  * @var \ICanBoogie\Session $session
  */

if (in_array($_SERVER['REQUEST_METHOD'], [ 'POST', 'PUT', 'DELETE' ]))
{
	$token = isset($_POST['_session_token']) ? $_POST['_session_token'] : null;
	
	if ($session->verify_token($token))
	{
		// Token is verified, we can proceed with the request.
	}
	else
	{
		// Token is not verified, we should throw an exception.
	}
}
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

The package is documented as part of the [ICanBoogie][] framework [documentation][]. You can
generate the documentation for the package and its dependencies with the `make doc` command. The
documentation is generated in the `build/docs` directory. [ApiGen](http://apigen.org/) is required.
The directory can later be cleaned with the `make clean` command.





## Testing

The test suite is ran with the `make test` command. [PHPUnit](https://phpunit.de/) and
[Composer](http://getcomposer.org/) need to be globally available to run the suite. The command
installs dependencies as required. The `make test-coverage` command runs test suite and also creates
an HTML coverage report in `build/coverage`. The directory can later be cleaned with the `make
clean` command.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://img.shields.io/travis/ICanBoogie/Session/master.svg)](http://travis-ci.org/ICanBoogie/Session)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/Session/master.svg)](https://coveralls.io/r/ICanBoogie/Session)





## License

**icanboogie/session** is licensed under the New BSD License - See the [LICENSE](LICENSE) file for details.





[documentation]:  http://api.icanboogie.org/session/latest/
[Session]:        http://api.icanboogie.org/session/latest/class-ICanBoogie.Session.html
[SessionSegment]: http://api.icanboogie.org/session/latest/class-ICanBoogie.SessionSegment.html
[ICanBoogie]: https://github.com/ICanBoogie/ICanBoogie
[CSRF]:                       https://en.wikipedia.org/wiki/Cross-site_request_forgery
[Cross-Site Request Forgery]: https://en.wikipedia.org/wiki/Cross-site_request_forgery
