<?php

namespace App\Models;

class PageContent extends BaseModel
{
	public static function findByName(string $name): ?self
	{
		return static::where('name', $name)->first();
	}
}
