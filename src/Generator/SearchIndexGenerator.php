<?php

namespace Blep\Generator;

class SearchIndexGenerator
{
    private array $data;
    private string $outputDir;

    public function __construct(array $data, string $outputDir)
    {
        $this->data = $data;
        $this->outputDir = rtrim($outputDir, '/');
    }

    public function generate(): void
    {
        $index = [];
        $id = 0;

        foreach ($this->data as $topic => $subtopics) {
            foreach ($subtopics as $subtopic => $items) {
                foreach ($items as $item) {
                    if ($item['type'] === 'detail') {
                        $commits = [];
                        if (!empty($item['history'])) {
                            foreach ($item['history'] as $h) {
                                $commits[] = $h['message'];
                            }
                        }

                        $index[] = [
                            'id' => ++$id,
                            'topic' => $topic,
                            'subtopic' => $subtopic,
                            'detail' => $item['text'],
                            'rationale' => $item['rationale'] ?? '',
                            'code' => $item['snippet'] ?? '',
                            'file' => $item['file'],
                            'line' => $item['line'],
                            'author' => $item['blame']['author'] ?? '',
                            'commits' => implode(' ', $commits),
                            'topicSlug' => $this->slug($topic),
                            'subtopicSlug' => $this->slug($subtopic)
                        ];
                    }
                }
            }
        }

        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }

        file_put_contents(
            "{$this->outputDir}/search-index.json",
            json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function slug(string $text): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($text)));
    }
}
