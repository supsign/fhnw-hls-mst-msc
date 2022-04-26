<?php

namespace App\Services\PageContents;

use App\Helpers\GeneralHelper;
use App\Models\PageContent;

class PageContentService
{
    public function __invoke(array $contentKeys): array
    {
        $contents = PageContent::whereIn('name', $contentKeys)->get();
        $result = [];

        foreach ($contentKeys AS $contentKey) {
            $contentKey = GeneralHelper::snakeToCamelCase($contentKey);

            foreach ($contents AS $content) {
                if (GeneralHelper::snakeToCamelCase($content->name) === $contentKey) {
                    $result[$contentKey] = $content->content;
                    continue 2;
                }
            }

            if (!isset($result[$contentKey])) {
                $result[$contentKey] = null;
            }
        }

        return $result;
    }
}