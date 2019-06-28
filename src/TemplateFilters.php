<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2016 ublaboo <ublaboo@paveljanda.com>
 * @author      Pavel Janda <me@paveljanda.com>
 * @package     Ublaboo
 */

namespace Ublaboo\ApiDocu;

use Parsedown;

class TemplateFilters
{
	private static $parsedown;

	public static function common(string $filter): ?string
	{
		if (method_exists(__CLASS__, $filter)) {
			$args = func_get_args();
			array_shift($args);
			return call_user_func_array([__CLASS__, $filter], $args);
		}
	}


	public static function description(string $text): string
	{
		self::$parsedown = new Parsedown();
		return self::$parsedown->text($text);
	}
}
