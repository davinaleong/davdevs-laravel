<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;

class MarkdownService
{
    protected CommonMarkConverter $converter;

    public function __construct()
    {
        $this->converter = new CommonMarkConverter([
            'html_input' => 'strip', // strip raw HTML from Markdown source — no XSS via post content
            'allow_unsafe_links' => false,
        ]);
    }

    public function toHtml(?string $markdown): string
    {
        if (blank($markdown)) {
            return '';
        }

        return (string) $this->converter->convert($markdown);
    }
}
