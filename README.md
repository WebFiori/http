# RESTEasy
A simple library for creating RESTful web APIs. 
It includes inputs feltering and data validation in addion to creating user-defined inputs filters.

<p align="center">
  <a href="https://travis-ci.com/github/WebFiori/restEasy">
    <img src="https://travis-ci.com/WebFiori/restEasy.svg?branch=master">
  </a>
  <a href="https://codecov.io/gh/WebFiori/restEasy">
    <img src="https://codecov.io/gh/WebFiori/restEasy/branch/master/graph/badge.svg" />
  </a>
  <a href="https://sonarcloud.io/dashboard?id=WebFiori_restEasy">
      <img src="https://sonarcloud.io/api/project_badges/measure?project=WebFiori_restEasy&metric=alert_status" />
  </a>
  <a href="https://github.com/WebFiori/restEasy/releases">
      <img src="https://img.shields.io/github/release/WebFiori/restEasy.svg?label=latest" />
  </a>
  <a href="https://packagist.org/packages/webfiori/rest-easy ">
      <img src="https://img.shields.io/packagist/dt/webfiori/rest-easy?color=light-green">
  </a>
</p>

## API Docs
This library is a part of <a>WebFiori Framework</a>. To access API docs of the library, you can visid the following link: https://webfiori.com/docs/webfiori/restEasy .

## The Idea
The idea of the library is as follows, when a client performs a request to a web service, he is usually intersted in performing specific action. Related actions are kept in one place as a set of web services (e.g. CRUD operations on a reasorce). The client can pass arguments (or parameters) to the end point (the services set) in request body or as a query string.

An end point is represented by the class [`AbstractWebService`](https://webfiori.com/docs/webfiori/restEasy/AbstractWebService) and a set of web service (or end ponts) are grouped using the class [`WebServicesManager`](https://webfiori.com/docs/webfiori/restEasy/WebServicesManager). Also, body parameters represented by the class [`RequestParameter`](https://webfiori.com/docs/webfiori/restEasy/RequestParameter).

## Features
* Full support for creating REST services that supports JSON as response.
* Support for basic data filtering and validation.
* The ability to create custom filters based on the need.
* Support for `application/json` content type for `POST` and `PUT` request methods.

## Supported PHP Versions
The library support all versions starting from version 5.6 up to version 7.4.

## Installation
If you are using composer to collect your dependencies, you can simply include the following entry in your 'composer.json' file to get the latest release of the library:

``` json
{
    "require": {
        "webfiori/rest-easy":"*"
    }
}
```
Note that the <a href="https://github.com/usernane/jsonx">WebFiori Json</a> library will be included with the installation files as this library is depending on it. 

Another option is to download the latest release manually from <a href="https://github.com/usernane/restEasy/releases">Release</a>.

## Usage
For more information on how to use the library, [check here](https://github.com/usernane/wf-docs/blob/master/web-services.md)
