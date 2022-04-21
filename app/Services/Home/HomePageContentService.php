<?php

namespace App\Services\Home;

use App\Helpers\GeneralHelper;
use App\Models\PageContent;

class HomePageContentService
{
    protected array $contentKeys = ['intro_title', 'intro_content', 'intro_link'];

    public function __invoke(): array
    {
        $contents = PageContent::whereIn('name', $this->contentKeys)->get();
        $result = [];

        $i = 0;

        foreach ($this->contentKeys AS $contentKey) {
            $result[GeneralHelper::snakeToCamelCase($contentKey)] = $contents[$i++]->content ?? null;
        }

        return $result;
    }
}