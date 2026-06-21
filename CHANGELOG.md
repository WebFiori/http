# Changelog

## [6.0.2](https://github.com/WebFiori/http/compare/v6.0.1...v6.0.2) (2026-06-21)


### Features

* **annotations:** add path property to #[RestController] attribute ([cdd937a](https://github.com/WebFiori/http/commit/cdd937a8b8e1e1ece90dc314bc82b9e9dc643bbe)), closes [#141](https://github.com/WebFiori/http/issues/141)
* OpenAPI annotations, path routing, and JsonI serialization ([4b3efd3](https://github.com/WebFiori/http/commit/4b3efd3093afddefbc3caeae4a15d909e0842cf2))
* **openapi:** add #[ApiResponse] annotation for declarative response descriptions ([b1efc1e](https://github.com/WebFiori/http/commit/b1efc1eed5936b59b57007bd4cf0d48ee4f248ec)), closes [#143](https://github.com/WebFiori/http/issues/143)
* **openapi:** add namespace scanning and built-in OpenAPISpecService ([4e4b230](https://github.com/WebFiori/http/commit/4e4b2309dd78f02f38a8c9539379bbe497af5873)), closes [#144](https://github.com/WebFiori/http/issues/144)
* **response:** serialize JsonI objects directly without metadata in #[ResponseBody] ([2a4e8b9](https://github.com/WebFiori/http/commit/2a4e8b9a96b1a18620f14303b9664c132de07af8)), closes [#142](https://github.com/WebFiori/http/issues/142)


### Miscellaneous Chores

* Merge pull request [#146](https://github.com/WebFiori/http/issues/146) from WebFiori/dev ([79a25aa](https://github.com/WebFiori/http/commit/79a25aab1a6f7ae012d6579aece81985ef0589b0))

## [6.0.1](https://github.com/WebFiori/http/compare/v6.0.0...v6.0.1) (2026-06-13)


### Miscellaneous Chores

* JsonX Bump-up ([10837f2](https://github.com/WebFiori/http/commit/10837f2faeaa49395d6f68385102ef9a77e5f33e))

## [6.0.0](https://github.com/WebFiori/http/compare/v5.0.8...v6.0.0) (2026-06-01)


### ⚠ BREAKING CHANGES

* **#113:** Validation error responses now return HTTP 422 instead of 404.

### Features

* **#111:** allow isAuthorized() to return string as denial reason ([9186547](https://github.com/WebFiori/http/commit/91865474f66287bf1002223e778ce4aec5730882))
* **#111:** allow isAuthorized() to return string as denial reason ([19a1b8f](https://github.com/WebFiori/http/commit/19a1b8f50ecf703198835bf0ff61330426995708))
* **#113:** change validation errors to 422 and add custom error messages ([26341e2](https://github.com/WebFiori/http/commit/26341e24b66edafbe6f393b72240e6f313ea7dec))
* **#114:** add allowed-values and pattern validation to RequestParameter ([f86f5c8](https://github.com/WebFiori/http/commit/f86f5c85ada5b9858d25983f0b9a9e79bcf8b64e))
* **#114:** add allowed-values and pattern validation to RequestParameter ([e74d92d](https://github.com/WebFiori/http/commit/e74d92d80014b37bca9657d561e1bcd21e3a9071))
* **#114:** add allowed-values and pattern validation to RequestParameter ([161ee39](https://github.com/WebFiori/http/commit/161ee39d483a53c2d5a8bf999a621a84714212c9))
* **#115:** add cross-field validation with #[Validate] attribute ([2793917](https://github.com/WebFiori/http/commit/2793917b3388689930ed02bd2943f25d64f0851d))
* **#115:** add cross-field validation with #[Validate] attribute ([851d6a0](https://github.com/WebFiori/http/commit/851d6a0356e1182762e0e4700daea8002dd11457))
* **#116:** add reusable parameter sets with ParameterSet interface ([5bd309a](https://github.com/WebFiori/http/commit/5bd309ae3337dbfaa8d5bd508c7d4ed89604dd64))
* **#116:** add reusable parameter sets with ParameterSet interface ([90e6914](https://github.com/WebFiori/http/commit/90e6914c14c30faa263d79f02ffe6e52b3555dfe))
* **#117:** #[RequiresAuth] checks SecurityContext::isAuthenticated() directly ([b60aa66](https://github.com/WebFiori/http/commit/b60aa66028027164e396d7c4c06d90c8c13bac26))
* **#117:** #[RequiresAuth] checks SecurityContext::isAuthenticated() directly ([2598fb9](https://github.com/WebFiori/http/commit/2598fb98f2b8363ef4a6aceb85df1b87eed20f5d))
* **#121:** create RequestProcessor class ([7810da5](https://github.com/WebFiori/http/commit/7810da574c445c553db0c710b991430b60732080))
* **#121:** create RequestProcessor class ([d06f00e](https://github.com/WebFiori/http/commit/d06f00ed204018fe369c0849801e49926db15e4f))
* add content negotiation with #[Produces] attribute ([bc33b35](https://github.com/WebFiori/http/commit/bc33b351e27c333d11e0da1d849d769336ab717a))
* add content negotiation with #[Produces] attribute and MediaType constants ([0826ce1](https://github.com/WebFiori/http/commit/0826ce1b1e276aeb37ce06f2da5ab7f8bc754a3c))
* add ServiceTestCase and TestResponse for simplified service testing ([01ce116](https://github.com/WebFiori/http/commit/01ce116d6bd3769eb00748e474942ff96fdf4719))


### Bug Fixes

* **#112:** EMAIL param injection returns null for JSON body requests ([8147a7d](https://github.com/WebFiori/http/commit/8147a7d83e8b63685d7261be2afec981294e608e))
* **#112:** EMAIL param injection returns null for JSON body requests ([4425b60](https://github.com/WebFiori/http/commit/4425b605312393af88e986cf4b1f347c5929d8d4))
* **#132:** preserve native PHP boolean false in APIFilter ([e28ef9f](https://github.com/WebFiori/http/commit/e28ef9f0204f3a76f2196bf0f4b8be89c1f5edf5))
* add missing PatchMapping annotation class ([8107785](https://github.com/WebFiori/http/commit/81077850b5db5148f135f713808169321de3c748))
* remove [@depends](https://github.com/depends) from tests for PHPUnit 12 compatibility ([2b9a2e3](https://github.com/WebFiori/http/commit/2b9a2e3a4daf92a3e377923112584cc0573993fb))
* remove duplicate property and method declarations ([56cd360](https://github.com/WebFiori/http/commit/56cd360d9f0aab8558179f96c4a619c16c35d426))


### Miscellaneous Chores

* exclude tests and examples from SonarCloud duplication detection ([04f7d99](https://github.com/WebFiori/http/commit/04f7d99f6816e11aef046591a7f1ce4978b46e3f))
* unify phpunit config, update CI to workflows v1.2.5 ([4a45a68](https://github.com/WebFiori/http/commit/4a45a68bf03157464000e0f98146dd9faec01545))

## [5.0.8](https://github.com/WebFiori/http/compare/v5.0.7...v5.0.8) (2026-05-05)


### Features

* add ResponseEntity class for dynamic HTTP status codes with #[ResponseBody] ([09162ce](https://github.com/WebFiori/http/commit/09162ce7f8f6954101334e8a9a79a8212b6cf676)), closes [#107](https://github.com/WebFiori/http/issues/107)


### Bug Fixes

* resolve method parameter injection for hyphenated request param names ([83caed4](https://github.com/WebFiori/http/commit/83caed403b1ccc4f51f84faaa84ce4f326cdceeb)), closes [#106](https://github.com/WebFiori/http/issues/106)


### Miscellaneous Chores

* Merge pull request [#110](https://github.com/WebFiori/http/issues/110) from WebFiori/dev ([fa077cd](https://github.com/WebFiori/http/commit/fa077cdd649719ce6eca3f745a2f14cd0afd38cc))

## [5.0.7](https://github.com/WebFiori/http/compare/v5.0.6...v5.0.7) (2026-05-03)


### Features

* OpenAPI Support for Annotations ([5b99eb5](https://github.com/WebFiori/http/commit/5b99eb593c8b2df765a76572189cc24eb8d9af90))


### Bug Fixes

* Annotations on `processRequest` ([23ac100](https://github.com/WebFiori/http/commit/23ac1004b852f96a3444607d0be38149b451d5fd))


### Miscellaneous Chores

* Version Update ([442f336](https://github.com/WebFiori/http/commit/442f336dbc3a973b2d63e161a94f1d63bb99f775))

## [5.0.6](https://github.com/WebFiori/http/compare/v5.0.5...v5.0.6) (2026-04-28)


### Bug Fixes

* Response Object Usage ([34ce8e8](https://github.com/WebFiori/http/commit/34ce8e8d472c19d516cd17bb395d60617141bca8)), closes [#97](https://github.com/WebFiori/http/issues/97)

## [5.0.5](https://github.com/WebFiori/http/compare/v5.0.4...v5.0.5) (2026-03-05)


### Bug Fixes

* Deprecation Warning ([a37745f](https://github.com/WebFiori/http/commit/a37745f62e1bec3109542224832b8ea19db5dbc8))


### Miscellaneous Chores

* Added PHP CS Fixer ([6de946b](https://github.com/WebFiori/http/commit/6de946bff25ef2b518769f7f61a6f39ba9ce4dd0))
* Merge pull request [#94](https://github.com/WebFiori/http/issues/94) from WebFiori/dev ([9164496](https://github.com/WebFiori/http/commit/916449626e655e196a4a4161ffa260c3a1df986c))
* Run CS Fixer ([d2d015b](https://github.com/WebFiori/http/commit/d2d015b4a89191245d1f63f38c55d23429d53a65))

## [5.0.4](https://github.com/WebFiori/http/compare/v5.0.3...v5.0.4) (2026-01-14)


### Miscellaneous Chores

* Updated Required PHP Version ([c6c8b2a](https://github.com/WebFiori/http/commit/c6c8b2a3783c2f787ca5340c2ebb64a1f1e78dbc))

## [5.0.3](https://github.com/WebFiori/http/compare/v5.0.2...v5.0.3) (2026-01-14)


### Bug Fixes

* Return Empty String if No Path ([05566fb](https://github.com/WebFiori/http/commit/05566fb96835b1ea3d62ce5b4da79368d0ea2c57))

## [5.0.2](https://github.com/WebFiori/http/compare/v5.0.1...v5.0.2) (2026-01-14)


### Miscellaneous Chores

* Fail Tests on Deprecations ([b3710ca](https://github.com/WebFiori/http/commit/b3710ca552b3c5a5cdaf21f184500c29b5df8270))
* Revert Fail on Deprications ([747b3ad](https://github.com/WebFiori/http/commit/747b3ad8c367ade2ded3968fe8ea0269785d7359))

## [5.0.1](https://github.com/WebFiori/http/compare/v5.0.0...v5.0.1) (2026-01-13)


### Bug Fixes

* Auto-Discover Null Path ([e5fe330](https://github.com/WebFiori/http/commit/e5fe330a990198604617e161f0db3d3fb2f96e70))

## [5.0.0](https://github.com/WebFiori/http/compare/v4.0.0...v5.0.0) (2025-12-30)


### Features

* Add OpenAPI Specs to Request Param ([c8c820d](https://github.com/WebFiori/http/commit/c8c820de9240df8c2db98a64f66fd15ade75939e))
* Add Support for Attributes ([3108687](https://github.com/WebFiori/http/commit/310868753f46bd36eb3253a8b3a42bdb83120c41))
* Add Support for Custom Filter in Attr ([7472ae0](https://github.com/WebFiori/http/commit/7472ae0800986f33a9c3f93b8d15ae636b3acd35))
* Add Support for Parameter Request Methods ([05b1b04](https://github.com/WebFiori/http/commit/05b1b045a120f3d04c8611428db31fb699638469))
* Added `HttpMessage` ([474db18](https://github.com/WebFiori/http/commit/474db18efdfdfe4f06ff31b3d851bdea4fd9dbc4))
* Added `RequestURI` ([b7f4e25](https://github.com/WebFiori/http/commit/b7f4e25c023349d1457aaa1b1d62263f450b7f3b))
* Added Annotations to Web Services ([2d0e6fc](https://github.com/WebFiori/http/commit/2d0e6fc0bdbbb85d8eef41ecaaec477bc8be35cb))
* Added Content Type to `RequestBody` ([b218200](https://github.com/WebFiori/http/commit/b218200dcaea84faaa4f0e843b8721db66bda12f))
* Added Exceptions ([71c38ec](https://github.com/WebFiori/http/commit/71c38ecaeb739deeae40e7d16d69f5ae277a7553))
* Added OpenAPI Schema ([02ad503](https://github.com/WebFiori/http/commit/02ad50399532075388958805b0b68f1d51440b78))
* Added Support for Annotations ([0ad4d94](https://github.com/WebFiori/http/commit/0ad4d946c79f30047b2d3d370b067b781efd5b9b))
* Annotated Auth ([4241fdf](https://github.com/WebFiori/http/commit/4241fdf42ecf73c0a9448c66b904376e093b5fc9))
* Auto Mapping ([b635249](https://github.com/WebFiori/http/commit/b635249c7e5ba109afb03e55e81ac6742c27205c))
* Auto-Discovery ([12e546a](https://github.com/WebFiori/http/commit/12e546ae6e7705cd430bebc093a8430824cbdd3e))
* Detection of Duplicated Mappings ([30da6a9](https://github.com/WebFiori/http/commit/30da6a9862e8a639480a78ad09bb57947a430721))
* Dev ([64b67f7](https://github.com/WebFiori/http/commit/64b67f7ad79432f59e3c2b0e200901de0241b0e0))
* Do Not Allow Use of Reserved Names ([dc473db](https://github.com/WebFiori/http/commit/dc473dbb7d0ad905457845986faf95ebf77c2f55))
* Enhanced Security Context ([9cd90b8](https://github.com/WebFiori/http/commit/9cd90b870c71ffc4412285b2ed1b24e1d7e47607))
* External Doc Obj of Open API ([decf743](https://github.com/WebFiori/http/commit/decf743382e40ebcd2572894b6b52e39fe6a39fc))
* Invoke Service Auto for Single Reg ([ef029a1](https://github.com/WebFiori/http/commit/ef029a12169758fbc857f561c7f150c02c88c49e))
* Open API Classes ([af63ed3](https://github.com/WebFiori/http/commit/af63ed35076715bf1d8c72b3d325934d52ab5bc6))
* Open API Obj ([2ac8ce9](https://github.com/WebFiori/http/commit/2ac8ce9fec1129327b2dc615e0a0381f2319e5db))
* Open api support ([b1d265d](https://github.com/WebFiori/http/commit/b1d265d1f2e24267a24fe7737fb73006aedb2384))
* Parameters Injection ([0262390](https://github.com/WebFiori/http/commit/026239084ecb4d7c2db789942f52c20d9561288d))


### Bug Fixes

* Fixes to Request Class ([a86a946](https://github.com/WebFiori/http/commit/a86a946cf9137631b91c405da4feb35a978004b2))
* Get URI With Fragment or Query String ([43c13d1](https://github.com/WebFiori/http/commit/43c13d1608aa5a4ee23b22e25c013926ce0947e7))
* Multiple Fixes to Core ([d2c9c4a](https://github.com/WebFiori/http/commit/d2c9c4a1c78cdae71082bdf722ecc7dde16895b2))
* PHPUnit 9 Missing Method ([e83112c](https://github.com/WebFiori/http/commit/e83112ccc48a3ba2be521bf90119bba2b5d245f4))
* Populating PUT Data ([fd6a106](https://github.com/WebFiori/http/commit/fd6a106229c74041b0e9297c2bf6dddb92340c8b))
* PUT and PATCH Request Methods ([ad1c121](https://github.com/WebFiori/http/commit/ad1c121e400af0fc5420e513002a63379769d132))
* Rename Variable to Remove Conflect ([bd41190](https://github.com/WebFiori/http/commit/bd4119013f203ca584fcd6e6602c22fe53bab9c6))
* URI Parameter ([4effd3a](https://github.com/WebFiori/http/commit/4effd3a7c1b3ae86bda27debe106fe3f6977ad15))
* Use of Null as Standalone Type ([68e54b5](https://github.com/WebFiori/http/commit/68e54b5f7b9160b843bcf594f9ca6c6f6afd9421))


### Miscellaneous Chores

* Added PHP 8.5 to README ([dcac7b3](https://github.com/WebFiori/http/commit/dcac7b3289b54320541cb230cc7176946f8b16a8))
* Added Samples ([08eaa97](https://github.com/WebFiori/http/commit/08eaa97238c147647e24773b7e15025dba8cd4eb))
* Config Changes ([6718e2a](https://github.com/WebFiori/http/commit/6718e2aa38d19b2b6082010f718065f0dcd916d3))
* Docs ([56b62a2](https://github.com/WebFiori/http/commit/56b62a20f1ddeb1901e87b07166404366f403817))
* Fix Main Readme code ([bfbf5a2](https://github.com/WebFiori/http/commit/bfbf5a224c038e4cbed80e4f8c2e606647aef936))
* Ignore Tests Cache ([4d57d85](https://github.com/WebFiori/http/commit/4d57d854a8b1acdf93c09a4758b99ca311b0907d))
* **main:** release 4.0.0 ([3c0d662](https://github.com/WebFiori/http/commit/3c0d66213a3aafb0712b46bbd753deb0001b76a4))
* Merge pull request [#80](https://github.com/WebFiori/http/issues/80) from WebFiori/dev ([6fa2a60](https://github.com/WebFiori/http/commit/6fa2a60225ff82c084ffbfab391f1087affc9af7))
* Merge pull request [#82](https://github.com/WebFiori/http/issues/82) from WebFiori/dev ([a09ecd8](https://github.com/WebFiori/http/commit/a09ecd89089211615eb57c9cf381a59e2d63e7c8))
* Merge pull request [#83](https://github.com/WebFiori/http/issues/83) from WebFiori/open-api-support ([b1d265d](https://github.com/WebFiori/http/commit/b1d265d1f2e24267a24fe7737fb73006aedb2384))
* Merge pull request [#84](https://github.com/WebFiori/http/issues/84) from WebFiori/feat-attriputes ([3108687](https://github.com/WebFiori/http/commit/310868753f46bd36eb3253a8b3a42bdb83120c41))
* Merge pull request [#85](https://github.com/WebFiori/http/issues/85) from WebFiori/ci-updates ([e9c5126](https://github.com/WebFiori/http/commit/e9c512656398729296729c66512ba0e131e397dd))
* Merge pull request [#86](https://github.com/WebFiori/http/issues/86) from WebFiori/feat-attriputes ([2dbf191](https://github.com/WebFiori/http/commit/2dbf191bd82cea00b755418cc00ae3d768d89e33))
* Merge pull request [#87](https://github.com/WebFiori/http/issues/87) from WebFiori/dev ([64b67f7](https://github.com/WebFiori/http/commit/64b67f7ad79432f59e3c2b0e200901de0241b0e0))
* Merge pull request [#88](https://github.com/WebFiori/http/issues/88) from WebFiori/docs ([56b62a2](https://github.com/WebFiori/http/commit/56b62a20f1ddeb1901e87b07166404366f403817))
* Merge pull request [#89](https://github.com/WebFiori/http/issues/89) from WebFiori/dev ([4f75857](https://github.com/WebFiori/http/commit/4f758571ebe96bcd55568e7adbc59dfad250a1e5))
* Multiple Fixes ([24f0865](https://github.com/WebFiori/http/commit/24f0865ea42953caede6b898996fc1e39082da2b))
* Multiple Fixes ([66effd1](https://github.com/WebFiori/http/commit/66effd1130c0c852af551c790a71ee60d74cc101))
* No Longer Needed ([d388604](https://github.com/WebFiori/http/commit/d388604536837dc20c2cce1f8a168b1bf0482ae2))
* OpenAPI ([2bfdf8a](https://github.com/WebFiori/http/commit/2bfdf8ab7f3d79457c975ba5376b9553547289ea))
* Rename `AbstractWebService` to `WebService` ([0ea983a](https://github.com/WebFiori/http/commit/0ea983a99f3deaefe932961721af64256e8f7dfb))
* Run CS Fixer ([acc1c7a](https://github.com/WebFiori/http/commit/acc1c7a6b066f2660e96abb3436a33d36300d8b2))
* Updated License Headers ([03023ea](https://github.com/WebFiori/http/commit/03023eaf2d11b19e283cb09731b2575a28f2b27f))
* Updated License Headers ([37acd59](https://github.com/WebFiori/http/commit/37acd5900f349ddac50cdae611e44e23a0c8b3ca))
* Updated README + License ([bb39d7c](https://github.com/WebFiori/http/commit/bb39d7c5626a9c02442a463f680b187777713db5))

## [4.0.0](https://github.com/WebFiori/http/compare/v3.6.1...v4.0.0) (2025-08-06)


### Miscellaneous Chores

* Copy of Folders ([03de388](https://github.com/WebFiori/http/commit/03de3889af334ad1f085cce30be8c41a51a64284))
* release v4.0.0 ([956c14c](https://github.com/WebFiori/http/commit/956c14c7d89f5d6ce7090319720d5d7c5767c2c6))
* Rename Folders ([a7249d3](https://github.com/WebFiori/http/commit/a7249d3260e0b9c9a0f517a9dfe5f3eba3cd8024))
* Rename of File ([a6cce14](https://github.com/WebFiori/http/commit/a6cce14b1cb6f4b676102a9ebd853ce597683440))
* Rename of Folders ([ad28047](https://github.com/WebFiori/http/commit/ad28047a7035049ae1295a7691ea6fcb7613c34e))
* Renamed Folders ([2ea43ec](https://github.com/WebFiori/http/commit/2ea43ec6088edc3fd6243f8d8a51be8e071ca16b))
* Updated Code Sample ([c8420e7](https://github.com/WebFiori/http/commit/c8420e722ced6e6ae30ae7d5214d19f1512173f8))

## [3.6.1](https://github.com/WebFiori/http/compare/v3.6.0...v3.6.1) (2025-08-04)


### Bug Fixes

* Get Requested Path ([e0d834e](https://github.com/WebFiori/http/commit/e0d834ee340a963634ebf1400b94f7e6dce0bdd3))

## [3.6.0](https://github.com/WebFiori/http/compare/v3.5.1...v3.6.0) (2024-12-24)


### Features

* Added Format Method to API Test Case ([621e9ed](https://github.com/WebFiori/http/commit/621e9ed07364e94b7a4ce102397ffd27ac7879a6))


### Bug Fixes

* Fix to Output File Path ([d2e2072](https://github.com/WebFiori/http/commit/d2e2072b7c6385b34614451eef883a5ae2013f8f))


### Miscellaneous Chores

* Updated .gitattributes ([594c617](https://github.com/WebFiori/http/commit/594c61781ab9413965bf210070fe2b78f84168b2))

## [3.5.0](https://github.com/WebFiori/http/compare/v3.4.1...v3.5.0) (2024-12-09)


### Features

* Added a Method to Get Cookie Value ([6d8e970](https://github.com/WebFiori/http/commit/6d8e97036c9e5a4fecf15b35770df120ac649c00))


### Bug Fixes

* Added Missing Request Method ([9336245](https://github.com/WebFiori/http/commit/9336245cd288355b7a462378a29b7c1c9869d9fe))

## [3.4.1](https://github.com/WebFiori/http/compare/v3.4.0...v3.4.1) (2024-12-01)


### Bug Fixes

* Bug on Adding Query String ([0234fdb](https://github.com/WebFiori/http/commit/0234fdb9b452255d8d180f29962eb9564c37632b))


### Miscellaneous Chores

* Added PHP 8.4 Status to README ([2bdb197](https://github.com/WebFiori/http/commit/2bdb197fed8ca579f0388e75840707aa5e553e6d))

## [3.4.0](https://github.com/WebFiori/http/compare/v3.3.15...v3.4.0) (2024-11-27)


### Features

* Added Ability to Mimic HTTP Request Headers ([f440e07](https://github.com/WebFiori/http/commit/f440e07d53fc08167e83550508af43029a4a8c70))

## [3.3.15](https://github.com/WebFiori/http/compare/v3.3.14...v3.3.15) (2024-11-20)


### Bug Fixes

* Add Missing Query String to URI ([fc69e5e](https://github.com/WebFiori/http/commit/fc69e5e317b63b543f92477502ee37fa768acfd2))


### Miscellaneous Chores

* Added Release Please to Workflow ([4db2020](https://github.com/WebFiori/http/commit/4db202003e771f0b50a0a28807719d023d0e70c6))
