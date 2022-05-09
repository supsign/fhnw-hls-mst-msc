<?php

namespace App\Models;

use Illuminate\Support\Collection;

class PageContent extends BaseModel
{

	public static function getContentByName(string $name): ?string
	{
		return static::findByName($name)?->content;
	}

	public static function findByName(array|string $name): Collection|self|null
	{
		if (is_array($name)) {
			return static::whereIn('name', $name)->get();
		}

		return static::where('name', $name)->first();
	}
}
