<?php

namespace Blep\Generator;

class HtmlGenerator
{
    private array $data;
    private string $outputDir;
    private string $siteTitle;
    private string $timestamp;
    private TemplateRenderer $renderer;
    private string $styles;

    public function __construct(array $data, string $outputDir, array $options = [])
    {
        $this->data = $data;
        $this->outputDir = rtrim($outputDir, '/');
        $this->siteTitle = $options['title'] ?? 'Business Logic Documentation';
        $this->timestamp = date('Y-m-d H:i:s');
        $this->renderer = new TemplateRenderer($options['templateFolder'] ?? null);
    }

    public function generate(): void
    {
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }

        $this->styles = $this->renderer->render('styles.css', []);

        $this->generateIndex();
        $this->generateSearch();
        $this->generateChangelog();
        foreach (array_keys($this->data) as $topic) {
            $this->generateTopicPage($topic);
        }
    }

    private function generateIndex(): void
    {
        $topicNames = array_keys($this->data);
        sort($topicNames);

        $topics = array_map(function (string $name): array {
            return ['name' => $name, 'slug' => $this->slug($name)];
        }, $topicNames);

        $html = $this->renderer->render('index.php', [
            'siteTitle' => $this->siteTitle,
            'timestamp' => $this->timestamp,
            'styles'    => $this->styles,
            'topics'    => $topics,
        ]);

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
                $blameInfo = '';
                if (isset($firstDetail['blame'])) {
                    $date = date('Y-m-d H:i', $firstDetail['blame']['timestamp']);
                    $author = htmlspecialchars($firstDetail['blame']['author']);
                    $blameInfo = " — last updated $date by $author";
                }
                $content .= "<p class=\"src-location\">$src$blameInfo</p>\n";

                if (!empty($firstDetail['history'])) {
                    $historyItems = array_slice($firstDetail['history'], 0, 5);
                    $historyHtml = '';
                    foreach ($historyItems as $h) {
                        $hDate = date('Y-m-d H:i', $h['timestamp']);
                        $hAuthor = htmlspecialchars($h['author']);
                        $hMsg = htmlspecialchars($h['message']);
                        $hHash = substr($h['hash'], 0, 7);
                        $historyHtml .= "<li><code>$hHash</code> $hDate by $hAuthor — $hMsg</li>\n";
                    }
                    $content .= "<details class=\"history-block\"><summary>Recent Changes</summary><ul>$historyHtml</ul></details>\n";
                }
            }

            $content .= "<ul>\n";
            foreach ($items as $item) {
                if ($item['type'] === 'detail') {
                    $text = htmlspecialchars($item['text']);
                    $content .= "<li>$text";
                    if (!empty($item['rationale'])) {
                        $rationale = htmlspecialchars($item['rationale']);
                        $content .= "<details class=\"rationale-block\"><summary>Why?</summary><p>$rationale</p></details>";
                    }
                    $content .= "</li>\n";
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

        $html = $this->renderer->render('topic.php', [
            'siteTitle' => $this->siteTitle,
            'timestamp' => $this->timestamp,
            'styles'    => $this->styles,
            'topicName' => $topic,
            'slug'      => $slug,
            'content'   => $content,
        ]);

        file_put_contents("{$this->outputDir}/topic-$slug.html", $html);
    }

    private function generateChangelog(): void
    {
        $allChanges = [];

        foreach ($this->data as $topic => $subtopics) {
            foreach ($subtopics as $subtopic => $items) {
                foreach ($items as $item) {
                    if ($item['type'] === 'detail' && !empty($item['history'])) {
                        foreach ($item['history'] as $h) {
                            $allChanges[] = [
                                'timestamp'   => $h['timestamp'],
                                'topic'       => $topic,
                                'subtopic'    => $subtopic,
                                'detail'      => $item['text'],
                                'author'      => $h['author'],
                                'message'     => $h['message'],
                                'hash'        => substr($h['hash'], 0, 7),
                                'topicSlug'   => $this->slug($topic),
                                'subtopicSlug' => $this->slug($subtopic)
                            ];
                        }
                    }
                }
            }
        }

        usort($allChanges, fn($a, $b) => $b['timestamp'] - $a['timestamp']);
        $allChanges = array_slice($allChanges, 0, 100);

        $content = '';
        foreach ($allChanges as $change) {
            $date    = date('Y-m-d H:i', $change['timestamp']);
            $topic   = htmlspecialchars($change['topic']);
            $subtopic = htmlspecialchars($change['subtopic']);
            $detail  = htmlspecialchars($change['detail']);
            $author  = htmlspecialchars($change['author']);
            $message = htmlspecialchars($change['message']);
            $hash    = $change['hash'];
            $link    = "topic-{$change['topicSlug']}.html#{$change['subtopicSlug']}";

            $content .= <<<ITEM
<div class="changelog-item">
  <div class="changelog-meta">
    <span class="changelog-date">$date</span>
    <span class="changelog-hash">$hash</span>
    <span class="changelog-author">$author</span>
  </div>
  <div class="changelog-topic"><a href="$link">$topic → $subtopic</a></div>
  <div class="changelog-detail">$detail</div>
  <div class="changelog-message">$message</div>
</div>

ITEM;
        }

        if (empty($content)) {
            $content = '<p>No change history available. Make sure your project is in a git repository.</p>';
        }

        $html = $this->renderer->render('changelog.php', [
            'siteTitle' => $this->siteTitle,
            'timestamp' => $this->timestamp,
            'styles'    => $this->styles,
            'content'   => $content,
        ]);

        file_put_contents("{$this->outputDir}/changelog.html", $html);
    }

    private function generateSearch(): void
    {
        $html = $this->renderer->render('search.php', [
            'siteTitle' => $this->siteTitle,
            'timestamp' => $this->timestamp,
            'styles'    => $this->styles,
        ]);

        file_put_contents("{$this->outputDir}/search.html", $html);
    }

    private function slug(string $text): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($text)));
    }
}
