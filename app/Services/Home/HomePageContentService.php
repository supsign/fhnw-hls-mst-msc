<?php

namespace App\Services\Home;

use App\Helpers\GeneralHelper;
use App\Models\PageContent;

class HomePageContentService
{
    protected array $contentKeys = ['intro_content', 'intro_title'];

    public function __invoke(): array
    {
        $contents = PageContent::whereIn('name', $this->contentKeys)->get();
        $result = [];

        $i = 0;

        // dump($this->contentKeys, $contents);

        foreach ($this->contentKeys AS $contentKey) {
            $result[GeneralHelper::snakeToCamelCase($contents[$i]->name)] = $contents[$i++]->content ?? null;
        }

        return $result;
    }
}