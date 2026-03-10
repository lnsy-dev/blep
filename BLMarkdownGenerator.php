<?php

class BLMarkdownGenerator
{
    private array $data;
    private string $outputDir;
    private string $siteTitle;
    private string $timestamp;

    public function __construct(array $data, string $outputDir, array $options = [])
    {
        $this->data = $data;
        $this->outputDir = rtrim($outputDir, '/');
        $this->siteTitle = $options['title'] ?? 'Business Logic Documentation';
        $this->timestamp = date('Y-m-d H:i:s');
    }

    public function generate(): void
    {
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }

        $this->generateIndex();
        foreach (array_keys($this->data) as $topic) {
            $this->generateTopicPage($topic);
        }
    }

    private function generateIndex(): void
    {
        $topics = array_keys($this->data);
        sort($topics);

        $md = "# {$this->siteTitle}\n\n";
        foreach ($topics as $topic) {
            $slug = $this->slug($topic);
            $md .= "- [{$topic}](topic-{$slug}.md)\n";
        }
        $md .= "\n---\n*Generated on {$this->timestamp}*\n";

        file_put_contents("{$this->outputDir}/index.md", $md);
    }

    private function generateTopicPage(string $topic): void
    {
        $slug = $this->slug($topic);
        $subtopics = $this->data[$topic];

        $md = "# {$topic}\n\n[← Back to Index](index.md)\n\n";

        foreach ($subtopics as $subtopicName => $items) {
            $subtopicSlug = $this->slug($subtopicName);
            $md .= "## {$subtopicName}\n\n";

            $firstDetail = null;
            foreach ($items as $item) {
                if ($item['type'] === 'detail' && $firstDetail === null) {
                    $firstDetail = $item;
                    break;
                }
            }

            if ($firstDetail) {
                $src = "{$firstDetail['file']}:{$firstDetail['line']}";
                $blameInfo = '';
                if (isset($firstDetail['blame'])) {
                    $date = date('Y-m-d H:i', $firstDetail['blame']['timestamp']);
                    $author = $firstDetail['blame']['author'];
                    $blameInfo = " — last updated {$date} by {$author}";
                }
                $md .= "*{$src}{$blameInfo}*\n\n";
            }

            foreach ($items as $item) {
                if ($item['type'] === 'detail') {
                    $md .= "- {$item['text']}\n";
                } elseif ($item['type'] === 'see') {
                    $refTopicSlug = $this->slug($item['topic']);
                    $refSubtopicSlug = $this->slug($item['subtopic']);
                    $md .= "- See: [{$item['topic']} → {$item['subtopic']}](topic-{$refTopicSlug}.md#{$refSubtopicSlug})\n";
                }
            }
            $md .= "\n";

            if ($firstDetail && isset($firstDetail['snippet'])) {
                $md .= "```php\n{$firstDetail['snippet']}\n```\n\n";
            }
        }

        $md .= "---\n*Generated on {$this->timestamp}*\n";

        file_put_contents("{$this->outputDir}/topic-{$slug}.md", $md);
    }

    private function slug(string $text): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($text)));
    }
}
