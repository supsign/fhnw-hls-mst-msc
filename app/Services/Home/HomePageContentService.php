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

        foreach ($this->contentKeys AS $contentKey) {
            $contentKey = GeneralHelper::snakeToCamelCase($contentKey);

            foreach ($contents AS $content) {
                if (GeneralHelper::snakeToCamelCase($content->name) === $contentKey) {
                    $result[$contentKey] = $content->content;
                }
            }

            if (!isset($result[$contentKey])) {
                $result[$contentKey] = null;
            }
        }

        return $result;
    }
}