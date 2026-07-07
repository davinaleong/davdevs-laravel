<?php

namespace App\Services\Migration;

class ContentTypeMap
{
    /**
     * Old content folder => content_types.slug, per 10-migration-plan.md §2 / ContentTypeSeeder.
     * Deliberately excludes technical-demos (test fixtures) and ebooks (deferred).
     */
    public const FOLDERS = [
        'articles' => 'article',
        'projects' => 'project',
        'tools' => 'tool',
        'notebooks' => 'notebook',
        'knowledge-sharing' => 'knowledge-sharing',
        'fem' => 'fem',
        'sermons' => 'sermon',
        'static' => 'page',
    ];
}
