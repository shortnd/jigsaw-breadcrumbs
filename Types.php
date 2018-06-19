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
	 *
	 */
	public $template = 'bootstrap';

	public function home()
	{
		return ['Home', '/'];
	}

	public function posts()
	{
		return Builder::parent('home')
				->push('Posts');
	}

	public function category($category)
	{
		return Builder::parent('posts')
				->push([ $category->title, $category->getUrl() ]);
	}

	public function post($post)
	{
		return Builder::parent('posts')
				->push([ $post->title, $post->getUrl() ]);
	}

	public function resource($resource)
	{
		return Builder::parent('post', $resource['post'])
				->push(['pdf', '/files/13dje545dfm']);
	}
}
