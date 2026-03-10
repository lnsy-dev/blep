<?php

class BLHtmlGenerator
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

        $links = '';
        foreach ($topics as $topic) {
            $slug = $this->slug($topic);
            $links .= "<li><a href=\"topic-$slug.html\">" . htmlspecialchars($topic) . "</a></li>\n";
        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$this->siteTitle}</title>
{$this->getStyles()}
</head>
<body>
<h1>{$this->siteTitle}</h1>
<ul>
$links</ul>
<footer>Generated on {$this->timestamp}</footer>
</body>
</html>
HTML;

        file_put_contents("{$this->outputDir}/index.html", $html);
    }

    private function generateTopicPage(string $topic): void
    {
        $slug = $this->slug($topic);
        $subtopics = $this->data[$topic];

        $content = '';
        foreach ($subtopics as $subtopicName => $items) {
            $subtopicSlug = $this->slug($subtopicName);
            $content .= "<h2 id=\"$subtopicSlug\">" . htmlspecialchars($subtopicName) . "</h2>\n";

            $firstDetail = null;
            foreach ($items as $item) {
                if ($item['type'] === 'detail' && $firstDetail === null) {
                    $firstDetail = $item;
                    break;
                }
            }

            if ($firstDetail) {
                $src = htmlspecialchars("{$firstDetail['file']}:{$firstDetail['line']}");
                $content .= "<p class=\"src-location\">$src</p>\n";
            }

            $content .= "<ul>\n";
            foreach ($items as $item) {
                if ($item['type'] === 'detail') {
                    $text = htmlspecialchars($item['text']);
                    $content .= "<li>$text</li>\n";
                } elseif ($item['type'] === 'see') {
                    $refTopicSlug = $this->slug($item['topic']);
                    $refSubtopicSlug = $this->slug($item['subtopic']);
                    $refExists = isset($this->data[$item['topic']][$item['subtopic']]);
                    $class = $refExists ? '' : ' class="unresolved"';
                    $title = $refExists ? '' : ' title="Unresolved reference"';
                    $refText = htmlspecialchars($item['topic']) . ' → ' . htmlspecialchars($item['subtopic']);
                    $content .= "<li>See: <a href=\"topic-$refTopicSlug.html#$refSubtopicSlug\"$class$title>$refText</a></li>\n";
                }
            }
            $content .= "</ul>\n";

            if ($firstDetail && isset($firstDetail['snippet'])) {
                $snippet = htmlspecialchars($firstDetail['snippet']);
                $content .= "<details class=\"code-block\"><summary>code</summary><pre><code class=\"language-php\">$snippet</code></pre></details>\n";
            }
        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>$topic - {$this->siteTitle}</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/php.min.js"></script>
<script>hljs.highlightAll();</script>
{$this->getStyles()}
</head>
<body>
<nav><a href="index.html">← Back to Index</a></nav>
<h1>$topic</h1>
$content
<footer>Generated on {$this->timestamp}</footer>
</body>
</html>
HTML;

        file_put_contents("{$this->outputDir}/topic-$slug.html", $html);
    }

    private function getStyles(): string
    {
        return <<<CSS
<style>
body { font-family: 'Courier New', monospace; max-width: 900px; margin: 40px auto; padding: 0 20px; background: #fafafa; color: #222; line-height: 1.6; }
h1 { border-bottom: 2px solid #333; padding-bottom: 10px; }
h2 { margin-top: 30px; color: #444; }
ul { list-style: square; }
li { margin: 8px 0; }
a { color: #0066cc; text-decoration: none; }
a:hover { text-decoration: underline; }
.unresolved { color: #cc0000; text-decoration: line-through; }
.src-location { font-size: 0.85em; color: #666; font-style: italic; margin: 8px 0; }
details.code-block { margin: 16px 0; }
details.code-block summary { cursor: pointer; color: #0066cc; font-weight: bold; }
pre { background: #f6f8fa; border: 1px solid #d0d7de; border-radius: 6px; padding: 16px; overflow-x: auto; margin: 8px 0; }
code { font-family: 'Courier New', Consolas, monospace; font-size: 0.9em; }
nav { margin-bottom: 20px; }
footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #ccc; font-size: 0.9em; color: #666; }
@media (max-width: 600px) { body { margin: 20px auto; font-size: 14px; } }
</style>
CSS;
    }

    private function slug(string $text): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($text)));
    }
}
