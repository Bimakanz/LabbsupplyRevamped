<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use Illuminate\Support\Str;

class FaqContent
{
    /**
     * Turn the CMS FAQ HTML into groups of questions and answers.
     *
     * The Shopping FAQs page is authored as a flat stream of headings and
     * paragraphs. Keeping the parser here means the admin page stays the
     * source of truth while the storefront can use a richer layout.
     *
     * @return array{
     *     intro: array<int, string>,
     *     updated: string|null,
     *     groups: array<int, array{
     *         title: string,
     *         key: string,
     *         items: array<int, array{question: string, answer: string}>
     *     }>
     * }
     */
    public static function parse(?string $html): array
    {
        $html = trim((string) $html);

        if ($html === '') {
            return ['intro' => [], 'updated' => null, 'groups' => []];
        }

        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);

        $document->loadHTML(
            '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>'
            .'<body><div id="faq-root">'.$html.'</div></body></html>',
            LIBXML_HTML_NODEFDTD
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('faq-root');

        if (! $root) {
            return ['intro' => [], 'updated' => null, 'groups' => []];
        }

        $intro = [];
        $updated = null;
        $groups = [];
        $currentGroup = null;
        $currentQuestion = null;

        $flush = function () use (&$groups, &$currentGroup, &$currentQuestion): void {
            if ($currentQuestion === null || $currentGroup === null) {
                return;
            }

            $question = trim((string) ($currentQuestion['question'] ?? ''));

            if ($question !== '') {
                $groups[$currentGroup]['items'][] = [
                    'question' => $question,
                    'answer' => self::cleanAnswerHtml((string) ($currentQuestion['answer'] ?? '')),
                ];
            }

            $currentQuestion = null;
        };

        foreach ($root->childNodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($node->nodeName);

            if (in_array($tag, ['h2', 'h3', 'h4'], true)) {
                $flush();

                $title = self::cleanText($node->textContent);

                if ($title === '') {
                    continue;
                }

                if (Str::contains(Str::lower($title), ['masih punya pertanyaan', 'masih ada pertanyaan'])) {
                    $currentGroup = null;

                    continue;
                }

                $title = trim((string) preg_replace('/^\s*\d+\s*[\.\)]\s*/u', '', $title));
                $title = $title !== '' ? $title : 'Pertanyaan Lainnya';

                $baseKey = Str::slug($title) ?: 'pertanyaan-lainnya';
                $key = $baseKey;
                $suffix = 2;

                while (collect($groups)->contains('key', $key)) {
                    $key = $baseKey.'-'.$suffix++;
                }

                $groups[] = [
                    'title' => $title,
                    'key' => $key,
                    'items' => [],
                ];

                $currentGroup = array_key_last($groups);

                continue;
            }

            if ($tag !== 'p') {
                continue;
            }

            $text = self::cleanText($node->textContent);

            if ($text === '') {
                continue;
            }

            if (preg_match('/^terakhir\s+diperbarui\s*:\s*(.+)$/iu', $text, $matches)) {
                $updated = trim($matches[1]);

                continue;
            }

            if ($currentGroup === null) {
                $intro[] = $text;

                continue;
            }

            $inner = self::innerHtml($node);

            if (preg_match('/\bQ\s*:\s*/iu', $inner)) {
                $parts = preg_split('/\bA\s*:\s*/iu', $inner, 2);

                $questionHtml = $parts[0] ?? '';
                $answerHtml = $parts[1] ?? '';

                $question = self::cleanText(strip_tags($questionHtml));
                $question = trim((string) preg_replace('/^\s*Q\s*:\s*/iu', '', $question));

                $flush();

                $currentQuestion = [
                    'question' => $question,
                    'answer' => trim($answerHtml) !== '' ? self::stripAnswerPrefix($answerHtml) : '',
                ];

                continue;
            }

            if (preg_match('/^\s*A\s*:\s*/iu', $text)) {
                if ($currentQuestion === null) {
                    $currentQuestion = ['question' => '', 'answer' => ''];
                }

                $currentQuestion['answer'] = self::joinAnswer(
                    (string) $currentQuestion['answer'],
                    self::stripAnswerPrefix($inner)
                );

                continue;
            }

            if ($currentQuestion !== null) {
                $currentQuestion['answer'] = self::joinAnswer(
                    (string) $currentQuestion['answer'],
                    $inner
                );
            }
        }

        $flush();

        $groups = array_values(array_filter(
            $groups,
            fn (array $group): bool => $group['items'] !== []
        ));

        return [
            'intro' => $intro,
            'updated' => $updated,
            'groups' => $groups,
        ];
    }

    private static function innerHtml(DOMElement $node): string
    {
        $html = '';

        foreach ($node->childNodes as $child) {
            $html .= $node->ownerDocument->saveHTML($child);
        }

        return trim($html);
    }

    private static function stripAnswerPrefix(string $html): string
    {
        $html = preg_replace(
            '/^\s*(?:<br\s*\/?>)*\s*(?:<strong>\s*)?A\s*:\s*(?:<\/strong>)?/iu',
            '',
            $html
        ) ?? $html;

        $html = preg_replace('/^(?:\s*<br\s*\/?>\s*)+/iu', '', $html) ?? $html;

        return trim($html);
    }

    private static function joinAnswer(string $answer, string $next): string
    {
        $answer = trim($answer);
        $next = trim($next);

        if ($answer === '') {
            return $next;
        }

        if ($next === '') {
            return $answer;
        }

        return $answer.' '.$next;
    }

    private static function cleanAnswerHtml(string $html): string
    {
        $html = trim($html);

        if ($html === '') {
            return '';
        }

        return preg_replace_callback(
            '/<a\s+([^>]*?)href=""([^>]*)>(.*?)<\/a>/is',
            function (array $matches): string {
                $label = trim(strip_tags(html_entity_decode($matches[3], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                $href = null;

                if (filter_var($label, FILTER_VALIDATE_EMAIL)) {
                    $href = 'mailto:'.$label;
                } elseif (preg_match('/^[+\d\s\-().]+$/', $label)) {
                    $href = 'tel:'.preg_replace('/[^\d+]/', '', $label);
                }

                if (! $href) {
                    return $matches[0];
                }

                return '<a '.$matches[1].'href="'.$href.'"'.$matches[2].'>'.$matches[3].'</a>';
            },
            $html
        ) ?? $html;
    }

    private static function cleanText(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xC2\xA0", ' ', $text);

        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }
}
