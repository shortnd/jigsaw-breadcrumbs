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
	public function push($segment, $link = null)
	{
		if (is_null($link)) {
			$link = strtolower($segment);
		}
		$this->segments[] = [$segment, $link];

		return $this;
	}

	/**
	 * Adds the given segment to those currently available and slugs it up
	 *
	 * @param string $segment
	 * @param string $separator
	 *
	 * @return array
	 */
	public function pushAndSlug($segment, $separator = null)
	{
		if (is_null($separator)) {
			$separator = '-';
		}

		$link = str_replace(' ', $separator, strtolower($segment));
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
	public static function make($segment, $link = null)
	{
		$builder = new Builder;
		return $builder->push($segment, $link);
	}

	/**
	 * Creates a new builder instance
	 * Adds the given segment and slugs it up
	 *
	 * @param string $segment
	 * @param string $link
	 *
	 * @return object \Plugin\Breadcrumbs\Builder
	 */
	public static function makeAndSlug($segment, $separator = null)
	{
		$builder = new Builder;
		return $builder->pushAndSlug($segment, $separator);
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
		return $types->$name($param);
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
