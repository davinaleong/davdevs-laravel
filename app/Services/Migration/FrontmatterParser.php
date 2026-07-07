<?php

namespace App\Services\Migration;

use Symfony\Component\Yaml\Yaml;

class FrontmatterParser
{
    /**
     * @return array{frontmatter: array, body: string}
     */
    public static function parse(string $path): array
    {
        $contents = file_get_contents($path);

        if (! preg_match('/^---\s*\R(.*?)\R---\s*\R?(.*)$/s', $contents, $matches)) {
            return ['frontmatter' => [], 'body' => $contents];
        }

        try {
            $frontmatter = Yaml::parse($matches[1]);
        } catch (\Throwable) {
            $frontmatter = [];
        }

        return [
            'frontmatter' => is_array($frontmatter) ? $frontmatter : [],
            'body' => trim($matches[2] ?? ''),
        ];
    }
}
