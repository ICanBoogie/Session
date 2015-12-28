# Session

[![Release](https://img.shields.io/packagist/v/icanboogie/session.svg)](https://packagist.org/packages/icanboogie/session)
[![Build Status](https://img.shields.io/travis/ICanBoogie/Session/master.svg)](http://travis-ci.org/ICanBoogie/Session)
[![HHVM](https://img.shields.io/hhvm/icanboogie/session.svg)](http://hhvm.h4cc.de/package/icanboogie/session)
[![Code Quality](https://img.shields.io/scrutinizer/g/ICanBoogie/Session/master.svg)](https://scrutinizer-ci.com/g/ICanBoogie/Session)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/Session/master.svg)](https://coveralls.io/r/ICanBoogie/Session)
[![Packagist](https://img.shields.io/packagist/dt/icanboogie/session.svg)](https://packagist.org/packages/icanboogie/session)

The **icanboogie/session** package manages PHP sessions.





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





[ControllerBindings]:                  http://api.icanboogie.org/bind-routing/0.2/class-ICanBoogie.Binding.Routing.ControllerBindings.html
[Response]:                            http://api.icanboogie.org/http/2.5/class-ICanBoogie.HTTP.Response.html
[Request]:                             http://api.icanboogie.org/http/2.5/class-ICanBoogie.HTTP.Request.html
[RequestDispatcher]:                   http://api.icanboogie.org/http/2.5/class-ICanBoogie.HTTP.RequestDispatcher.html
[documentation]:                       http://api.icanboogie.org/routing/2.5/
[ActionNotDefined]:                    http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.ActionNotDefined.html
[ActionTrait]:                         http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.Controller.ActionTrait.html
[Controller]:                          http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.Controller.html
[Controller\BeforeActionEvent]:        http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.Controller.BeforeActionEvent.html
[Controller\ActionEvent]:              http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.Controller.ActionEvent.html
[ControllerNotDefined]:                http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.ControllerNotDefined.html
[FormattedRoute]:                      http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.FormattedRoute.html
[Pattern]:                             http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.Pattern.html
[PatternNotDefined]:                   http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.PatternNotDefined.html
[ResourceTrait]:                       http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.Controller.ResourceTrait.html
[Route]:                               http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.Route.html
[Route\RescueEvent]:                   http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.Route.RescueEvent.html
[RouteCollection]:                     http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.RouteCollection.html
[RouteDispatcher]:                     http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.RouteDispatcher.html
[RouteDispatcher\BeforeDispatchEvent]: http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.RouteDispatcher.BeforeDispatchEvent.html
[RouteDispatcher\DispatchEvent]:       http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.RouteDispatcher.DispatchEvent.html
[RouteMaker]:                          http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.RouteMaker.html
[RouteNotDefined]:                     http://api.icanboogie.org/routing/2.5/class-ICanBoogie.Routing.RouteNotDefined.html
[ICanBoogie]:                          https://github.com/ICanBoogie/ICanBoogie
[icanboogie/bind-routing]:             https://github.com/ICanBoogie/bind-routing
[icanboogie/view]:                     https://github.com/ICanBoogie/View
[RESTful]:                             https://en.wikipedia.org/wiki/Representational_state_transfer
