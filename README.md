# WebFiori HTTP
A simple library for creating RESTful web APIs in adition to providing utilities for handling HTTP request and response. 
It includes inputs feltering and data validation in addion to creating user-defined inputs filters.

<p align="center">
  <a href="https://github.com/WebFiori/http/actions">
    <img src="https://github.com/WebFiori/http/actions/workflows/php83.yml/badge.svg?branch=master">
  </a>
  <a href="https://codecov.io/gh/WebFiori/http">
    <img src="https://codecov.io/gh/WebFiori/http/branch/master/graph/badge.svg" />
  </a>
  <a href="https://sonarcloud.io/dashboard?id=WebFiori_http">
      <img src="https://sonarcloud.io/api/project_badges/measure?project=WebFiori_http&metric=alert_status" />
  </a>
  <a href="https://github.com/WebFiori/http/releases">
      <img src="https://img.shields.io/github/release/WebFiori/http.svg?label=latest" />
  </a>
  <a href="https://packagist.org/packages/webfiori/http">
      <img src="https://img.shields.io/packagist/dt/webfiori/http?color=light-green">
  </a>
</p>

## Supported PHP Versions
|                                                                                        Build Status                                                                                         |
|:-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------:|
| <a target="_blank" href="https://github.com/WebFiori/http/actions/workflows/php70.yml"><img src="https://github.com/WebFiori/http/actions/workflows/php70.yml/badge.svg?branch=master"></a> |
| <a target="_blank" href="https://github.com/WebFiori/http/actions/workflows/php71.yml"><img src="https://github.com/WebFiori/http/actions/workflows/php71.yml/badge.svg?branch=master"></a> |
| <a target="_blank" href="https://github.com/WebFiori/http/actions/workflows/php72.yml"><img src="https://github.com/WebFiori/http/actions/workflows/php72.yml/badge.svg?branch=master"></a> |
| <a target="_blank" href="https://github.com/WebFiori/http/actions/workflows/php73.yml"><img src="https://github.com/WebFiori/http/actions/workflows/php73.yml/badge.svg?branch=master"></a> |
| <a target="_blank" href="https://github.com/WebFiori/http/actions/workflows/php74.yml"><img src="https://github.com/WebFiori/http/actions/workflows/php74.yml/badge.svg?branch=master"></a> |
| <a target="_blank" href="https://github.com/WebFiori/http/actions/workflows/php80.yml"><img src="https://github.com/WebFiori/http/actions/workflows/php80.yml/badge.svg?branch=master"></a> |
| <a target="_blank" href="https://github.com/WebFiori/http/actions/workflows/php81.yml"><img src="https://github.com/WebFiori/http/actions/workflows/php81.yml/badge.svg?branch=master"></a> |
| <a target="_blank" href="https://github.com/WebFiori/http/actions/workflows/php82.yml"><img src="https://github.com/WebFiori/http/actions/workflows/php82.yml/badge.svg?branch=master"></a> |
| <a target="_blank" href="https://github.com/WebFiori/http/actions/workflows/php83.yml"><img src="https://github.com/WebFiori/http/actions/workflows/php83.yml/badge.svg?branch=master"></a> |

## API Docs
This library is a part of <a>WebFiori Framework</a>. To access API docs of the library, you can visid the following link: https://webfiori.com/docs/webfiori/http .

## Terminology

Following terminology is used by the library: 
| Term | Definition|
|:------------:|:-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------:|
| Web Service | A single end pont that implements a REST service. It is represented as an instance of the class `AbstractWebService`. |
| Services Manager  | An entity which is used to manage a set of web services. Represented by the class `WebServicesManager`. |
| Request Parameter | A way to pass values from a client such as a web browser to the server. Represented by the class `RequestParameter`. |

## The Idea

The idea of the library is as follows, when a client performs a request to a web service, he is usually intersted in performing specific action. Related actions are kept in one place as a set of web services (e.g. CRUD operations on a reasorce). The client can pass arguments (or parameters) to the end point in request body as `POST` or `PUT` request method or as a query string when using `GET` or `DELETE`.

An end point is represented by the class [`AbstractWebService`](https://webfiori.com/docs/webfiori/http/AbstractWebService) and a set of web service (or end ponts) are grouped using the class [`WebServicesManager`](https://webfiori.com/docs/webfiori/http/WebServicesManager). Also, body parameters represented by the class [`RequestParameter`](https://webfiori.com/docs/webfiori/http/RequestParameter).

## Features
* Full support for creating REST services that supports JSON as request and response.
* Support for basic data filtering and validation.
* The ability to create custom filters based on the need.

## Installation
If you are using composer to collect your dependencies, you can simply include the following entry in your 'composer.json' file to get the latest release of the library:

``` json
{
    "require": {
        "webfiori/http":"*"
    }
}
```
Note that the <a href="https://github.com/WebFiori/json">WebFiori Json</a> library will be included with the installation files as this library is depending on it. 

Another option is to download the latest release manually from <a href="https://github.com/WebFiori/http/releases">Release</a>.

## Usage
For more information on how to use the library, [check here](https://github.com/WebFiori/wf-docs/blob/master/web-services.md)
