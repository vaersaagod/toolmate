ToolMate plugin for Craft CMS
===

Is that a tool in your pocket, or are you just happy to see me, mate!
  
![Screenshot](resources/plugin_logo.png)

## Requirements

This plugin requires Craft CMS 3.0.0 or later. 

## Installation

To install the plugin, either install it from the plugin store, or follow these instructions:

1. Install with composer via `composer require vaersaagod/toolmate` from your project directory.
2. Install the plugin in the Craft Control Panel under Settings → Plugins, or from the command line via `./craft install/plugin toolmate`.

---

## Configuring

ToolMate can be configured by creating a file named `toolmate.php` in your Craft config folder, 
and overriding settings as needed. 

### publicRoot [string]
*Default: `null`*
  
Sets the public webroot that is used by inline and stamp on servers 
where `$_SERVER['DOCUMENT_ROOT']` and `@webroot` is incorrect.  

### enableMinify [bool]
*Default: `true`*  

Enables/disables all minifying.

---

## Template variables

### craft.toolmate.inline(filename [, remote=false])

```
{{ craft.toolmate.inline('/assets/critical.css') }}
```

### craft.toolmate.stamp(filename [, mode = 'file', type = 'ts'])

```
{# /assets/bundle.1522425799.js #}
{{ craft.tool.stamp('/assets/bundle.js') }}

{# /assets/1522425799/bundle.js #}
{{ craft.tool.stamp('/assets/bundle.js', 'folder') }}

{# /assets/5140247221/bundle.js #}
{{ craft.tool.stamp('/assets/bundle.js', 'folder', 'hash') }}

{# /assets/bundle.js?ts=1522425799 #}
{{ craft.tool.stamp('/assets/bundle.js', 'query') }}

{# 1522425799 #}
{{ craft.tool.stamp('/assets/bundle.js', 'tsonly') }}

```

### craft.toolmate.setCookie(params [, secure = false])

```
{% do craft.toolmate.setCookie({ name: 'testing', value: 'Just testing!' }) %}
{% do craft.toolmate.setCookie({ name: 'testingsecure', value: { lorem: 'ipsum', dolor: 'sit amet' } }, true) %}

{% set params = {
    name: 'cookiename',
    value: 'thevalue',
    expire: 0,
    path: '/',
    domain: '',
    secure: false,
    httpOnly: false,
    sameSite: null,
} %}

{% do craft.toolmate.setCookie(params) %}
```

### craft.toolmate.getCookie(name [, secure = false])

```
{{ craft.toolmate.getCookie('testing') }}
{{ dump(craft.toolmate.getCookie('testingsecure', true)) }}
```

### craft.toolmate.getVideoEmbed(url [, params = []])

```
{% set videoEmbed = craft.toolmate.getVideoEmbed(videoUrl, {
    youtube_enablejsapi: 1,
    youtube_rel: 0,
    youtube_showinfo: 0,
    youtube_controls: 1,
    youtube_autoplay: 0,
    youtube_modestbranding: 1,
    youtube_playsinline: 0,
    vimeo_byline: 0,
    vimeo_title: 0,
    vimeo_autoplay: 0,
    vimeo_portrait: 0
}) %}
```

---

## Twig tags

### minify

```
<style>    
    {% minify css %}
        .lorem {
            width: 200px;
        }
        .ipsum {
            padding: 0px;
        }
    {% endminify %}
</style>

<script>
    {% minify js %}
        var myFunction = function () {
            console.log('Some inline JS');
        }
    {% endminify %}
</script>

{% minify html %}
    <div>
        <p>Some html</p>
    </div>
{% endminify %}
```

---

## Twig functions

### inline(filename [, remote=false])

See `craft.toolmate.inline`.

### stamp(filename [, mode = 'file', type = 'ts'])

See `craft.toolmate.stamp`.

### setCookie(params [, secure = false])

See `craft.toolmate.setCookie`.

### getCookie(name [, secure = false])

See `craft.toolmate.getCookie`.

### getVideoEmbed(url [, params = []])

See `craft.toolmate.getCookie`.


---

## Price, license and support

The plugin is released under the MIT license. It's made for Værsågod and friends, and no support 
is given. Submitted issues are resolved if it scratches an itch. 

## Changelog

See [CHANGELOG.MD](https://raw.githubusercontent.com/vaersaagod/toolmate/master/CHANGELOG.md).

## Credits

Brought to you by [Værsågod](https://www.vaersaagod.no)

Icon designed by [Freepik from Flaticon](https://www.flaticon.com/authors/freepik).
