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

        foreach ($contents AS $content) {
            $result[GeneralHelper::snakeToCamelCase($content->name)] = $content->content;
        }

        return $result;
    }
}