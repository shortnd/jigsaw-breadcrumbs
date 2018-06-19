
# Jigsaw Breadcrumbs plugin

## Overview
This is a [Jigsaw](https://github.com/tightenco/jigsaw)  plugin for adding breadcrumbs to your pages.

## Installation
Autoload plugins folder

```json
"autoload": {
    "psr-4": {
        "Plugin\\": "plugins/"
    }
} 
```

In your jigsaw app's root:
-  create a plugins folder
`mkdir plugins`
- clone this repository in it
`cd plugins`
`git clone https://github.com/zerochip/jigsaw-breadcrumbs.git`

## Usage
Define your breadcrumbs in `/plugins/Breadcrumbs/Types.php`
```php
<?php
	
namespace Plugin\Breadcrumbs;

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

    public function home()
    {
        return Builder::make('Home', '/');
    }
}

```
The breadcrumb template to be used is spedified in `$template` more on that later.The name of the breadcrumb will be a method in that class.
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
Then in our blade template
```ruby
{{ $page->breadcrumbs('home') }}
```
will output
```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
    </ol>
</nav>
```
<a href="/">Home</a>

**Chaining breadcrumbs**
Breadcrumbs can be chained. Example of a breadcrump for categories page
```php
// plugins/Breadcrumbs/Types.php
public function categories()
{
    return Builder::parent('home')
            ->push('Categories);
}
```

in view
```ruby
{{ $page->breadcrumbs('categories') }}
```

output
<a href="/">Home</a> / <a href="/categories">Categories</a>

**Advanced chaining**
Multiple children can be chained to a breadcrumb in one call. Since breadcrumb definitions are method the take parameters.  lets take a step back and look at our helper
```php
'breadcrumbs' => function ($page, $type, $params = null) {
    return Plugin\Breadcrumbs\Render::for($type, $page, $params);
},
```
This means the first parametr of a breadcrumb will be jigsaw's `$page` variable
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

Let me teach you Jigsaw made by the wonderful people at [Tighten](https://tighten.co) real quick.
```
layout
```ruby
<h2>{{ $page->title }}</h2>
{{ $page->breadcrumbs('post', $categories->where('slug', $post->category)->first() }}
<hr>
@yield('postContent')
```
Sample output

<h2>Jigsaw quickstart tutorial</h2>
<a href="/">Home</a> / <a href="/categories">Categories</a> / <a href="/categories/tutorial">Tutorial</a> / <a href="/2018-06-19-jigsaw_quickstart_tutorial">Jigsaw quickstart tutorial</a>
<hr>
Let me teach you Jigsaw made by the wonderful people at <a href="https://tighten.co">Tighten</a> real quick
