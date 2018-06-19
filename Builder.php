<?php
	
namespace Plugin\Breadcrumbs;

class Builder
{
	/**
	 * Breadcrumb segments
	 *
	 * @var array
	 */
	public $segments;

	/**
	 * Makes a new build instance with segments from the given parent breadcrumb
	 *
	 * @param string $type The breadcrumb type
	 * @param mixed $type The breadcrumb type
	 *
	 * @return object \Plugin\Breadcrumbs\Builder
	 */
	public static function parent($type, $params = null)
	{
		$builder = new Builder;
		$builder->segments = Builder::load($type, $params);
		return $builder;
	}

	/**
	 * Adds the given segment to those currently available
	 *
	 * @param mixed $segment
	 *
	 * @return array
	 */
	public function push($segment)
	{
		if (is_array($segment)) {
			$this->segments[] = $segment;
		} else {
			$this->segments[] = [$segment, strtolower($segment)];
		}

		return $this->segments;
	}

	/**
	 * Loads segments into an array
	 *
	 * @param string $name
	 * @param mixed $params
	 *
	 * @return array
	 */
	public static function load($name, $params = null)
	{
		$types = new Types; 
		$segments = $types->$name($params);

		if (is_array($segments)) {
			if (is_array($segments[0])) {
				foreach ($segments as $segment) {
					$breadcrumbs[] = $segment;
				}
			} else {
				$breadcrumbs[] = $segments;
			}
		} else {
			$breadcrumbs[] = [$segments, strtolower($segments)];
		}

		return $breadcrumbs;
	}
}
