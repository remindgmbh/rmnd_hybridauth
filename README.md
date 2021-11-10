# REMIND - Hybridauth

Use hybridauth lib to connect to oauth services like facebook
@see https://hybridauth.github.io/

[travis-img]: https://img.shields.io/travis/remindgmbh/rmnd_hybridauth.svg?style=flat-square
[codecov-img]: https://img.shields.io/codecov/c/github/remindgmbh/rmnd_hybridauth.svg?style=flat-square
[php-v-img]: https://img.shields.io/packagist/php-v/remind/rmnd-hybridauth?style=flat-square
[github-issues-img]: https://img.shields.io/github/issues/remindgmbh/rmnd_hybridauth.svg?style=flat-square
[contrib-welcome-img]: https://img.shields.io/badge/contributions-welcome-blue.svg?style=flat-square
[license-img]: https://img.shields.io/github/license/remindgmbh/rmnd_hybridauth.svg?style=flat-square
[styleci-img]: https://styleci.io/repos/306292847/shield

[![travis-img]](https://travis-ci.com/github/remindgmbh/rmnd_hybridauth)
[![codecov-img]](https://codecov.io/gh/remindgmbh/rmnd_hybridauth)
[![styleci-img]](https://github.styleci.io/repos/306292847)
[![php-v-img]](https://packagist.org/packages/remind/rmnd-hybridauth)
[![github-issues-img]](https://github.com/remindgmbh/rmnd_hybridauth/issues)
[![contrib-welcome-img]](https://github.com/remindgmbh/rmnd_hybridauth/blob/master/CONTRIBUTING.md)
[![license-img]](https://github.com/remindgmbh/rmnd_hybridauth/blob/master/LICENSE)

---

## How do I get set up?

 * Install
 * Create TypoScript configuration
 * Add login links to templates
 * See tutorials in folder "Tutorials/" for more use cases

## Extension settings

Admin Tools > Settings > Extension Configuration > rmnd_hybridauth

@todo

## Secutiry aspects

@todo

## Routing Problems

@todo

## Coming next (TODO)

 * Search all folders for fe_users with provider
 * Search subfolders for existing fe_users with provider
 * Signal slot to set fields received from provider
 * Signal slot to configure provider settings instead via typoscript
 * Configuration file loader to prevent usage of typoscript for sensible information
 * Logout from hybridauth, hook into TYPO3 logout to disconnect hybridauth by deleting session and cookies
 * Use frontend usergroup settings for after-login behavior (if set)
 * Add provider connections to fe_user
 * Confirmation of creation of new account after provider connect
 * Backend module to generate and test urls
