# ToolMate Changelog

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
