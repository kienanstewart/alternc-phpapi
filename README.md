# License

See LICENSE.txt.

# Summary

An API for interacting with AlternC installations. Uses the rest/post APIs exposed by AlternC in version 3.1 and 3.5. This is meant to be used by other applications.

# Installation

From the root directory of the project, run:

    composer install

# Usage

@TODO Examples and usage information.

# API Documentation

The documentation for this project is generated using [phpDocumentor][2]. To generate the docs, run

    make phpdocs

from the root of the project.

# Tests

There are tests using phpunit. To run the tests execute

    phpunit

# Versioning

This project uses [Semantic Versioning 2.0.0][1]. Version numbers for this project also include a section for which AlternC branch they are tracking. The version string for branches and releases follows the structure [AlternC PHP API Version]-[AlternC Target Branch][-dev (optional)]

Example: 1.0.0-3.1[-dev]

  * 1.0.0: The version of this api
  * 3.1: The AlternC branch number (note: the AlternC branch is often something like stable-3.1)
  * [-dev]: An optional "-dev" postfix to denote a development branch or release

[1]: http://semver.org/spec/v2.0.0.html "Semantic Versioning 2.0.0"
[2]: https://phpdoc.org/ "phpDocumentor"
