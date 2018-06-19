
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

The breadcrumb template to be used is specified in teh property `$template` but more on that later.The name of the breadcrumb will be a method in that class.
Add composer autoload to our config file
```php
require __DIR__.'/vendor/autoload.php';
```

Then add a helper function for breadcrumbs
```php
'breadcrumbs' => function ($page, $type, $params = null) {
    return Plugin\Breadcrumbs\Render::for($type, $page, $params);
},
```

Breadcrumbs are constructed using the `Jigsaw\Breadcrumbs\Builder` class
```php
public function home()
{
    return Builder::make('Home', '/');
}
```
The first parameter is the text and the second is the url

Then in a blade template
```ruby
{{ $page->breadcrumbs('home') }}
```
The first parameter passed to a helper call will be the breadcrumb type.

will output
```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
    </ol>
</nav>
```
<a href="/">Home</a>
<hr>

The second parameter is optional. When the the second parameter is not given url is not given the first parameter is converted to lowercase and used as the url. 
```php
public function home()
{
    return Builder::make('Blog');
}
```

in blade template
```ruby
{{ $page->breadcrumbs('blog') }}
```

output
```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/blog">Blog</a></li>
    </ol>
</nav>
```
<a href="/blog">Blog</a>
<hr>

When the breadcrumb url is a slugged version of the text the method `makeAndSlug()` can be used. It works like make but the second parameter is the delimiter for generating the slug.
```php
public function home()
{
    return Builder::makeAndSlug('About Us', '_');
}
```
will output
```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/about_us">About Us</a></li>
    </ol>
</nav>
```
<a href="/about_us">About Us</a>
<hr>

The second parameter is optional. When left out the separator defaults to a dash ( - ).
```php
public function home()
{
    return Builder::makeAndSlug('About Us');
}
```

output
```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/about-us">About Us</a></li>
    </ol>
</nav>
```
<a href="/about-us">About Us</a>
<hr>

### Chaining breadcrumbs

Breadcrumbs can be chained. When chaining breadcrumbs we can refference the parent in using the builder `parent()` method. We the add the child  using the `push()` which works like `make()`.
Example of a breadcrump for categories page
```php
// plugins/Breadcrumbs/Types.php
public function categories()
{
    return Builder::parent('home')
            ->push('Categories);
}
```

in blade template
```ruby
{{ $page->breadcrumbs('categories') }}
```

output
```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><a href="/categories">Categories</a></li>
    </ol>
</nav>
```
<a href="/">Home</a> / <a href="/categories">Categories</a>
<hr>

There is also a `pushAndSlug()` method which works like `makeAndSlug()`,

### Using values in global $page
Since a breadcrumb type is a method it can take parameters. With the current setup in the helper function the first parameter passed to a breadcrumb type is jigsaw's `$page` global variable.
```php
// plugins/Breadcrumbs/Types.php
public function project($project)
{
    return Builder::make('Projects')
            ->push($project->title, $project->title);
}
```
The parameter `$project` is actually jigsaw's `$page`  
in blade template

Lets say we have a projects collection
```php
'projects' => [
    'path' => 'projects/{-title}'
],
```

and the follow front matter in a project
```
---
extends: _layouts.project
section: projectDescription
title: Jigsaw Breadcrumbs
---
```

in the blade template
```ruby
{{ $page->breadcrumbs('project') }}
```

output
```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><a href="/projects">Projects</a></li>
        <li class="breadcrumb-item"><a href="/projects/jigsaw-breadcrumbs">Jigsaw Breadcrumbs</a></li>
    </ol>
</nav>
```
<a href="/">Home</a> / <a href="/projects">Projects</a> / <a href="/projects/jigsaw-breadcrumbs">Jigsaw Breadcrumbs</a>
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
public function post($post, $category)
{
    return Builder::parent('categories')
        ->push($category->title, $category->slug)
        ->push($post->title, $post->slug);
}
```

Post front matter
```
---
extends: _layouts.post
section: postContent
title: Jigsaw quickstart tutorial
category: tutorial
published: 2017-06-14
---
```

layout
```ruby
{{ $page->breadcrumbs('post', $categories->where('slug', $post->category)->first() }}
```

output
```
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><a href="/categories">Categories</a></li>
        <li class="breadcrumb-item"><a href="/categories/tutorial">Tutorial</a></li>
        <li class="breadcrumb-item"><a href="/2018-06-19-jigsaw_quickstart_tutorial">Jigsaw quickstart tutorial</a></li>
    </ol>
</nav>
<p>Let me teach you Jigsaw made by the wonderful people at <a href="https://tighten.co">Tighten</a> real quick<p>
```
<a href="/">Home</a> / <a href="/categories">Categories</a> / <a href="/categories/tutorial">Tutorial</a> / <a href="/2018-06-19-jigsaw_quickstart_tutorial">Jigsaw quickstart tutorial</a>
<hr>

### Custom templates

Set the custom templates property in `/plugin-settings/Breadcrumbs/Types.php` to true
```php
public $custom_template = true;
```

*Template styling info will go here*
