<?php

namespace Jigsaw\Breadcrumbs;

use PluginSettings\Breadcrumbs\Types;

class Render
{
	/**
	 * Returns given breadcrumb type
	 *
	 * @param string $name
	 * @param mixed $params
	 *
	 * @return string
	 */
	public static function for($page, $name, $param)
	{
		$types = new Types;
		$segments = $types->$name($page, $param)->segments;
		$template = Builder::template();
		$linebreaks = strpos($template['wrapper'], '{{items-br}}');
		$placeholder = $linebreaks ? '{{items-br}}' : '{{items}}';
		$breadcrumbs = '';
		$i = 1;
		
		foreach ($segments as $segment) {
			$text = $segment[0];
			$link = $segment[1];

			$item = str_replace('{{link}}', $link, $template['item']);
			$item = str_replace('{{text}}', $text, $item);

			$breadcrumbs .= $item;

			if ($linebreaks && $i != count($segments)) {
				$breadcrumbs .= "\n";
			}

			$i++;
		}

		return str_replace($placeholder, $breadcrumbs, $template['wrapper']);
	}
}
