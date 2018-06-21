
# Jigsaw Breadcrumbs plugin

**Disclaimer:** This project is still a WIP so things aren't working yet

## Overview
This is a [Jigsaw](https://github.com/tightenco/jigsaw)  plugin for adding breadcrumbs to your pages.

## Installation
`composer require zerochip/jigsaw-breadcrumbs`

Autoload plugin-settings folder
```json
"autoload": {
    "psr-4": {
        "PluginSettings\\": "plugins-settings/"
    }
} 
```

## Usage
Define your breadcrumbs in `/plugin-settings/Breadcrumbs/Types.php`

```php
<?php
    
namespace PluginSettings\Breadcrumbs;

use Jigsaw\Breadcrumbs\Builder;

class Types
{
    /*
    |--------------------------------------------------------------------------
    | Breadcrumb types
    |--------------------------------------------------------------------------
    |
    | This is where you make breadcrumb types
    | Documentation -> https://zerochip.github.com/jigsaw-breadcrumbs
    |
    */

    /**
     * Breadcrumb template to be used
     */
    public $template = 'bootstrap4';

    /**
     * Set to true when using a custom template
     */
    public $custom_template = false;

    /**
     * Home page
     * @return object \Jigsaw\Breadcrumbs\Builder
     */
    public function home()
    {
        return Builder::make('Home', '/');
    }
}
```

The breadcrumb template to be used is specified in the property `$template` but more on that later.The name of the breadcrumb will be a method in that class.
Add composer autoload to our config file
```php
require __DIR__.'/vendor/autoload.php';
```

Then add a helper function for breadcrumbs
```php
'breadcrumbs' => function ($page, $type, $param = null) {
    return Plugin\Breadcrumbs\Render::for($type, $page, $param);
},
```

Breadcrumbs are constructed using the method `make`  in the `Jigsaw\Breadcrumbs\Builder` class
```php
public function home()
{
    return Builder::make('Home', '/');
}
```
The first parameter is the text and the second is the url.

Then in a blade template
```ruby
{{ $page->breadcrumbs('home') }}
```

Assuming the baseUrl is http://example.com will output
```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="http://example.com/">Home</a></li>
    </ol>
</nav>
```
<a href="http://example.com/">Home</a>
<hr>

### Chaining breadcrumbs

Breadcrumbs can be chained. When chaining breadcrumbs we can reference the parent in using the builder `parent()` method. We add the child  using `push()` which works like `make()`.

Example of a breadcrump for categories page
```php
// plugins/Breadcrumbs/Types.php
public function categories()
{
    return Builder::parent('home')
            ->push('Categories', '/categories');
}
```
The url must be a relative path. The when breadcrumbs are rendered the baseUrl set in jigsaw's config file is used. 

in blade template
```ruby
{{ $page->breadcrumbs('categories') }}
```

output
```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="http://example.com/">Home</a></li>
        <li class="breadcrumb-item"><a href="http://example.com/categories">Categories</a></li>
    </ol>
</nav>
```
<a href="http://example.com/">Home</a> / <a href="http://example.com/categories">Categories</a>
<hr>

### Using global $page variable
Since a breadcrumb type is a method it can take parameters. With the current setup in the helper function the first parameter passed to a breadcrumb type is jigsaw's `$page` global variable.
```php
// plugins/Breadcrumbs/Types.php
public function category($page)
{
    return Builder::parent('categories')
              ->push($page->title, $page->getPath());
}
```

an example category `tutorial.md`
```
---
extends: _layouts.tutorial
section: projectDescription
title: Jigsaw Breadcrumbs
---
```

in the blade template
```ruby
{{ $page->breadcrumbs('category') }}
```

output
```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="http://example.com/">Home</a></li>
        <li class="breadcrumb-item"><a href="http://example.com/categories">Categories</a></li>
        <li class="breadcrumb-item"><a href="http://example.com/categories/tutorial">Tutorial</a></li>
    </ol>
</nav>
```
<a href="http://example.com/">Home</a> / <a href="http://example.com/categories">Categories</a> / <a href="http://example.com/categories/tutorial">Tutorial</a>
<hr>

### Advanced chaining

Multiple breadcrumb items can be chained in one call. Taking a step back to our helper
```php
'breadcrumbs' => function ($page, $type, $params = null) {
    return Plugin\Breadcrumbs\Render::for($type, $page, $params);
},
```

The second parameter passed to a breadcrumb helper call will be passed as the second parameter of a breadcrumb type.
```php
// plugins/Breadcrumbs/Types.php
public function article($page, $category)
{
    return Builder::parent('category', $category)
        ->push($page->title, $page->getPath());
}
```

example article front matter
```
---
extends: _layouts.article
section: articleContent
title: Jigsaw quickstart tutorial
category: tutorial
published: 2017-06-14
---
```

layout
```ruby
{{ $page->breadcrumbs('article', $categories->where('slug', $post->category) }}
```

output
```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="http://example.com/">Home</a></li>
        <li class="breadcrumb-item"><a href="http://example.com/categories">Categories</a></li>
        <li class="breadcrumb-item"><a href="http://example.com/categories/tutorial">Tutorial</a></li>
        <li class="breadcrumb-item"><a href="http://example.com/article/2018-06-19-jigsaw_quickstart_tutorial">Jigsaw quickstart tutorial</a></li>
    </ol>
</nav>
```
<a href="http://example.com/">Home</a> / <a href="http://example.com/categories">Categories</a> / <a href="http://example.com/categories/tutorial">Tutorial</a> / <a href="http://example.com/article/2018-06-19-jigsaw_quickstart_tutorial">Jigsaw quickstart tutorial</a>
<hr>

Multiple breadcrumbs can also be chained at once. Lets say there's a projects collection
```php
// plugins/Breadcrumbs/Types.php
public function project($page)
{
    return Builder::parent('home',)
        ->push('Projects', '/projects');
        ->push($page->title, $page->getPath());
}
```

in blade template
```ruby
{{ $page->breadcrumbs('project') }}
```

example output
```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="http://example.com/">Home</a></li>
        <li class="breadcrumb-item"><a href="http://example.com/projects">Projects</a></li>
        <li class="breadcrumb-item"><a href="http://example.com/projects/jigsaw-breadcrumbs">Jigsaw breadcrumbs</a></li>
    </ol>
</nav>
```
<a href="http://example.com/">Home</a> / <a href="http://example.com/projects">Projects</a> / <a href="http://example.com/projects/jigsaw-breadcrumbs">Jigsaw breadcrumbs</a>
<hr>

### Custom templates

Set the custom templates property in `/plugin-settings/Breadcrumbs/Types.php` to true
```php
public $custom_template = true;
```

*Template styling info will go here*
