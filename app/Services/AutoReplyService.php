<?php

namespace App\Services;

use App\Models\AutoReplyRule;

class AutoReplyService
{
    public function findMatchingRule(string $content): ?AutoReplyRule
    {
        $content = $this->normalize($content);

        $rules = AutoReplyRule::query()
            ->where('is_active', true)
            ->whereJsonLength('keywords', '>', 0)
            ->get();

        foreach ($rules as $rule) {
            foreach ($rule->keywords as $keyword) {
                $keyword = $this->normalize($keyword);

                if (str_contains($content, $keyword)) {
                    return $rule;
                }
            }
        }

        return null;
    }

    public function getFallbackRule(): ?AutoReplyRule
    {
        return AutoReplyRule::query()
            ->where('is_active', true)
            ->whereJsonLength('keywords', 0)
            ->first();
    }

    private function normalize(string $text): string
    {
        return preg_replace(
            '/\s+/',
            ' ',
            trim(mb_strtolower($text))
        );
    }
}