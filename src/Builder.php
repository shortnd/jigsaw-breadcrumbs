<?php
	
namespace Jigsaw\Breadcrumbs;

use PluginSettings\Breadcrumbs\Types;

class Builder
{
	/**
	 * Breadcrumb segments
	 *
	 * @var array
	 */
	public $segments;

	/**
	 * Adds the given segment to those currently available
	 *
	 * @param string $segment
	 * @param string $link
	 *
	 * @return array
	 */
	public function push($segment, $link)
	{
		$this->segments[] = [$segment, $link];
		return $this;
	}

	/**
	 * Creates a new builder instance
	 * Adds the given segment
	 *
	 * @param string $segment
	 * @param string $link
	 *
	 * @return object \Plugin\Breadcrumbs\Builder
	 */
	public static function make($segment, $link)
	{
		$builder = new Builder;
		return $builder->push($segment, $link);
	}

	/**
	 * Makes a new build instance with segments from the given parent breadcrumb
	 *
	 * @param string $type The breadcrumb type
	 * @param mixed $param Parameter to be passed to the type
	 *
	 * @return object \Plugin\Breadcrumbs\Builder
	 */
	public static function parent($name, $param = null)
	{
		$types = new Types;
		if (is_null($param)) {
			return $types->$name();
		} else {
			return $types->$name($param);
		}
	}

	/**
	 * Gets the breadcrumb template
	 *
	 * @return array
	 */
	public static function template()
	{
		$types = new Types;
		$template_folder = __DIR__.'/../templates/'.$types->template;
		return [
			'wrapper' => file_get_contents($template_folder.'/wrapper.html'),
			'item' => file_get_contents($template_folder.'/item.html')
		];
	}
}
