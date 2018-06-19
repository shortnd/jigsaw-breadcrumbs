<?php
	
namespace Plugin\Breadcrumbs;

class Render
{
	public static function for($name, $params = null)
	{
		$segments = Builder::load($name, $params);
		
		foreach ($segments as $segment) {
			$text = $segment[0];
			$link = $segment[1];

			if (isset($notFirst)) {
				echo " > ";
			}

			if ($link[0] != '/') {
				$link = '/'.$link;
			}
			/*
			echo '<a href="'.$baseUrl.$link.'">$text</a>";
			*/
			echo "<a href='$link'>$text</a>";

			$notFirst = true;
		}
	}
}
