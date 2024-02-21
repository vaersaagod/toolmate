# ToolMate Changelog

## 1.5.0 - 2024-02-21
### Added
- Added support for Craft 5  

## 1.4.2 - 2023-01-24
### Fixed  
- Fixed missing property declaration for Toolmate's minify service  

## 1.4.1 - 2022-05-19
### Improved
- ToolMate now removes any hard-coded nonces or hashes set in the CSP config, for any directives that also contain the `unsafe-inline` policy
- ToolMate now avoids inadvertently creating CSP directives that could be empty, when adding nonces   

## 1.4.0 - 2022-05-14
### Added
- Craft domains (i.e. Craft ID and the plugin store API) are now automatically included in the `connect-src` directive for control panel requests
- `unsafe-inline` directives are now added automatically for Yii error pages   
### Fixed
- Fixes an issue where unhashed CSP nonces would not be included in the actual CSP header, on Craft 4.0 
### Changed
- Refactored logic concerning how and when the CSP header is set  

## 1.3.1 - 2022-05-12
### Fixed
- Fixed an issue where ToolMate failed to include the `'unsafe-inline'` policy resource for the `style-src` CSP directive, for site requests where the Yii debug toolbar is enabled  

## 1.3.0.3 - 2022-04-28
### Fixed
- Fixed issue in Settings introduced in the Craft 4 refactoring.

## 1.3.0.2 - 2022-04-28
### Fixed
- Fixed an issue where $_csp didn’t have a default value which caused error.

## 1.3.0.1 - 2022-04-28
### Fixed
- Fixed an issue with `schemaVersion` in main plugin file being typed too hard.

## 1.3.0 - 2022-04-21
### Added
- Added support for Craft 4

### Changed
- ToolMate now requires PHP 8.0+

## 1.2.0.1 - 2022-02-14
### Fixed
- Fixes an issue where Toolmate wasn't able to set the public web root path correctly

## 1.2.0 - 2022-02-13
### Added
- Added `Settings::csp` for configuring the Content-Security-Policy header
- Added the `cspNonce()` Twig function for outputting a CSP nonce attribute or value

## 1.1.0 - 2021-10-06

### Added  
- Added `Settings::embedCacheDuration`, which controls the cache duration for successful video embed responses, and (defaults to `craft\config\GeneralConfig::cacheDuration`)  
- Added `Settings::embedCacheDurationOnError`, which controls the cache duration for unsuccessful video embed responses (default 5 minutes)    
- Added support for a `cache_duration` parameter for the `ToolmateVariable::getVideoEmbed()` and `getVideoEmbed()` Twig function, which can be used to override the cache duration in Toolmate's settings  
- Added a boilerplate `config.php` to the plugin repo

### Improvements
- ToolMate now caches unsuccessful video embed responses separate from successful video embed responses    
- `Settings::publicRoot` now defaults to the `@webroot` alias, and falls back on `$_SERVER['DOCUMENT_ROOT']`   

### Fixed
- Fixes an issue where ToolMate would cache video embed request responses for much longer than intended   

### Changed
- Toolmate now does logging to its own log file `storage/logs/toolmate.log`

## 1.0.3 - 2021-05-19
### Improved  
- YouTube embeds will always set `rel=0` to prevent YouTube from pulling in "related" videos from other channels when the video ends  

## 1.0.2 - 2020-12-15
### Fixed
- Fixed issue where oembed URL's didn't use https. 

## 1.0.1 - 2020-12-14
### Fixed
- Fixed issue with empty maxwidth oembed param

## 1.0.0 - 2020-05-09

### Added
- Initial public release
