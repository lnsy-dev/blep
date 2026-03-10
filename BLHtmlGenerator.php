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
        $this->generateSearch();
        $this->generateChangelog();
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
<nav><a href="search.html">🔍 Search</a> | <a href="changelog.html">📋 Changelog</a></nav>
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
                $blameInfo = '';
                if (isset($firstDetail['blame'])) {
                    $date = date('Y-m-d H:i', $firstDetail['blame']['timestamp']);
                    $author = htmlspecialchars($firstDetail['blame']['author']);
                    $blameInfo = " — last updated $date by $author";
                }
                $content .= "<p class=\"src-location\">$src$blameInfo</p>\n";

                // Add history if available
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
                    
                    // Add rationale if available
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

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>$topic - {$this->siteTitle}</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css" media="(prefers-color-scheme: light)">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/base16/gruvbox-dark-hard.min.css" media="(prefers-color-scheme: dark)">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/php.min.js"></script>
<script>hljs.highlightAll();</script>
{$this->getStyles()}
</head>
<body>
<nav><a href="index.html">← Back to Index</a> | <a href="search.html">🔍 Search</a> | <a href="changelog.html">📋 Changelog</a></nav>
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
:root {
  color-scheme: light dark;
}
html { height: 100%; }
body { font-family: 'Courier New', monospace; max-width: 900px; margin: 0 auto; padding: 0 20px; background: #fafafa; color: #222; line-height: 1.6; min-height: 100vh; display: flex; flex-direction: column; }
body > *:not(footer) { flex-shrink: 0; }
h1 { border-bottom: 2px solid #333; padding-bottom: 10px; }
h2 { margin-top: 30px; color: #444; }
ul { list-style: square; }
li { margin: 8px 0; }
a { color: #0066cc; text-decoration: none; }
a:hover { text-decoration: underline; }
.unresolved { color: #cc0000; text-decoration: line-through; }
.src-location { font-size: 0.85em; color: #666; font-style: italic; margin: 8px 0; }
.subtitle { font-size: 0.95em; color: #666; margin-top: -10px; }
details.code-block { margin: 16px 0; }
details.code-block summary { cursor: pointer; color: #0066cc; font-weight: bold; }
details.history-block { margin: 12px 0; font-size: 0.9em; }
details.history-block summary { cursor: pointer; color: #0066cc; }
details.history-block ul { margin: 8px 0; }
details.history-block li { font-size: 0.9em; margin: 4px 0; }
details.rationale-block { display: inline-block; margin-left: 8px; font-size: 0.9em; }
details.rationale-block summary { cursor: pointer; color: #0066cc; font-style: italic; }
details.rationale-block p { margin: 4px 0; padding: 8px; background: #f0f0f0; border-left: 3px solid #0066cc; }
pre { background: #f6f8fa; border: 1px solid #d0d7de; border-radius: 6px; padding: 16px; overflow-x: auto; margin: 8px 0; }
code { font-family: 'Courier New', Consolas, monospace; font-size: 0.9em; }
nav { margin-bottom: 20px; }
footer { margin-top: auto; padding: 20px 0; border-top: 1px solid #ccc; font-size: 0.9em; color: #666; }
.changelog-item { border-left: 3px solid #0066cc; padding-left: 16px; margin-bottom: 24px; }
.changelog-meta { font-size: 0.85em; color: #666; margin-bottom: 4px; }
.changelog-date { font-weight: bold; margin-right: 12px; }
.changelog-hash { font-family: monospace; background: #f0f0f0; padding: 2px 6px; border-radius: 3px; margin-right: 12px; }
.changelog-author { font-style: italic; }
.changelog-topic { font-weight: bold; margin: 4px 0; }
.changelog-detail { margin: 4px 0; }
.changelog-message { font-size: 0.9em; color: #666; font-style: italic; margin-top: 4px; }
@media (prefers-color-scheme: dark) {
  body { background: #282828; color: #ebdbb2; }
  h1 { border-bottom-color: #a89984; }
  h2 { color: #d5c4a1; }
  a { color: #83a598; }
  .unresolved { color: #fb4934; }
  .src-location { color: #928374; }
  .subtitle { color: #928374; }
  details.code-block summary { color: #83a598; }
  details.history-block summary { color: #83a598; }
  details.rationale-block summary { color: #83a598; }
  details.rationale-block p { background: #3c3836; border-left-color: #83a598; }
  pre { background: #3c3836; border-color: #504945; color: #ebdbb2; }
  code { color: #ebdbb2; }
  footer { border-top-color: #504945; color: #928374; }
  .changelog-item { border-left-color: #83a598; }
  .changelog-meta { color: #928374; }
  .changelog-hash { background: #3c3836; color: #ebdbb2; }
  .changelog-message { color: #928374; }
}
@media (max-width: 600px) { body { margin: 20px auto; font-size: 14px; } }
</style>
CSS;
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
                                'timestamp' => $h['timestamp'],
                                'topic' => $topic,
                                'subtopic' => $subtopic,
                                'detail' => $item['text'],
                                'author' => $h['author'],
                                'message' => $h['message'],
                                'hash' => substr($h['hash'], 0, 7),
                                'topicSlug' => $this->slug($topic),
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
            $date = date('Y-m-d H:i', $change['timestamp']);
            $topic = htmlspecialchars($change['topic']);
            $subtopic = htmlspecialchars($change['subtopic']);
            $detail = htmlspecialchars($change['detail']);
            $author = htmlspecialchars($change['author']);
            $message = htmlspecialchars($change['message']);
            $hash = $change['hash'];
            $link = "topic-{$change['topicSlug']}.html#{$change['subtopicSlug']}";

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

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Changelog - {$this->siteTitle}</title>
{$this->getStyles()}
</head>
<body>
<nav><a href="index.html">← Back to Index</a> | <a href="search.html">🔍 Search</a></nav>
<h1>Changelog</h1>
<p class="subtitle">Recent changes to business logic documentation</p>
$content
<footer>Generated on {$this->timestamp}</footer>
</body>
</html>
HTML;

        file_put_contents("{$this->outputDir}/changelog.html", $html);
    }

    private function generateSearch(): void
    {
        $html = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Search - Business Logic Documentation</title>
<script src="https://cdn.jsdelivr.net/npm/fuse.js@7.0.0"></script>
<style>
:root { color-scheme: light dark; }
html { height: 100%; }
body { font-family: 'Courier New', monospace; max-width: 900px; margin: 0 auto; padding: 0 20px; background: #fafafa; color: #222; line-height: 1.6; min-height: 100vh; display: flex; flex-direction: column; }
body > *:not(footer) { flex-shrink: 0; }
h1 { border-bottom: 2px solid #333; padding-bottom: 10px; }
nav { margin-bottom: 20px; }
a { color: #0066cc; text-decoration: none; }
a:hover { text-decoration: underline; }
footer { margin-top: auto; padding: 20px 0; border-top: 1px solid #ccc; font-size: 0.9em; color: #666; }

#search-input { width: 100%; padding: 12px; font-size: 16px; font-family: 'Courier New', monospace; border: 2px solid #333; border-radius: 4px; margin-bottom: 20px; }
#search-input:focus { outline: none; border-color: #0066cc; }

.search-card { border: 1px solid #d0d7de; border-radius: 6px; padding: 16px; margin-bottom: 16px; background: #fff; }
.search-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.topic-badge { display: inline-block; background: #0066cc; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 0.85em; margin-right: 8px; }
.subtopic-title { font-weight: bold; font-size: 1.1em; margin: 8px 0; }
.detail-text { margin: 8px 0; }
.code-preview { background: #f6f8fa; border: 1px solid #d0d7de; border-radius: 4px; padding: 8px; margin: 8px 0; font-size: 0.85em; overflow-x: auto; max-height: 150px; overflow-y: auto; }
.meta-info { font-size: 0.85em; color: #666; margin-top: 8px; }
.view-link { display: inline-block; margin-top: 8px; color: #0066cc; font-weight: bold; }
.empty-state { text-align: center; padding: 40px; color: #666; }
mark { background: #ffeb3b; padding: 2px 4px; border-radius: 2px; }

@media (prefers-color-scheme: dark) {
  body { background: #282828; color: #ebdbb2; }
  h1 { border-bottom-color: #a89984; }
  a { color: #83a598; }
  footer { border-top-color: #504945; color: #928374; }
  #search-input { background: #3c3836; border-color: #a89984; color: #ebdbb2; }
  .search-card { background: #3c3836; border-color: #504945; }
  .search-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.3); }
  .topic-badge { background: #83a598; color: #282828; }
  .code-preview { background: #282828; border-color: #504945; color: #ebdbb2; }
  .meta-info { color: #928374; }
  .view-link { color: #83a598; }
  .empty-state { color: #928374; }
  mark { background: #d79921; color: #282828; }
}
</style>
</head>
<body>
<nav><a href="index.html">← Back to Index</a> | <a href="changelog.html">📋 Changelog</a></nav>
<h1>Search Documentation</h1>
<input type="text" id="search-input" placeholder="Search topics, details, code, authors... (Press / to focus)" autofocus>
<div id="results"></div>
<footer>Powered by Fuse.js</footer>

<script>
let fuse;
let searchIndex = [];

fetch('search-index.json')
  .then(r => r.json())
  .then(data => {
    searchIndex = data;
    fuse = new Fuse(searchIndex, {
      keys: [
        { name: 'topic', weight: 2 },
        { name: 'subtopic', weight: 1.5 },
        { name: 'detail', weight: 2 },
        { name: 'rationale', weight: 1 },
        { name: 'code', weight: 0.5 },
        { name: 'file', weight: 0.8 },
        { name: 'author', weight: 0.5 },
        { name: 'commits', weight: 0.3 }
      ],
      threshold: 0.4,
      includeScore: true,
      includeMatches: true
    });
  });

const searchInput = document.getElementById('search-input');
const resultsDiv = document.getElementById('results');

searchInput.addEventListener('input', (e) => {
  const query = e.target.value.trim();
  
  if (!query || !fuse) {
    resultsDiv.innerHTML = '';
    return;
  }

  const results = fuse.search(query).slice(0, 20);
  
  if (results.length === 0) {
    resultsDiv.innerHTML = '<div class="empty-state">No results found. Try different keywords.</div>';
    return;
  }

  resultsDiv.innerHTML = results.map(result => {
    const item = result.item;
    const link = `topic-${item.topicSlug}.html#${item.subtopicSlug}`;
    
    let detailText = item.detail;
    let codePreview = item.code ? item.code.split('\n').slice(0, 5).join('\n') : '';
    
    // Highlight matches
    if (result.matches) {
      result.matches.forEach(match => {
        if (match.key === 'detail') {
          detailText = highlightMatches(item.detail, match.indices);
        }
      });
    }

    return `
      <div class="search-card">
        <div>
          <span class="topic-badge">${escapeHtml(item.topic)}</span>
          <span class="subtopic-title">${escapeHtml(item.subtopic)}</span>
        </div>
        <div class="detail-text">${detailText}</div>
        ${item.rationale ? `<div class="meta-info"><strong>Why:</strong> ${escapeHtml(item.rationale)}</div>` : ''}
        ${codePreview ? `<div class="code-preview">${escapeHtml(codePreview)}</div>` : ''}
        <div class="meta-info">${item.file}:${item.line}${item.author ? ` • ${escapeHtml(item.author)}` : ''}</div>
        <a href="${link}" class="view-link">View in context →</a>
      </div>
    `;
  }).join('');
});

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function highlightMatches(text, indices) {
  let result = '';
  let lastIndex = 0;
  
  indices.forEach(([start, end]) => {
    result += escapeHtml(text.substring(lastIndex, start));
    result += '<mark>' + escapeHtml(text.substring(start, end + 1)) + '</mark>';
    lastIndex = end + 1;
  });
  
  result += escapeHtml(text.substring(lastIndex));
  return result;
}

// Keyboard shortcut
document.addEventListener('keydown', (e) => {
  if (e.key === '/' && document.activeElement !== searchInput) {
    e.preventDefault();
    searchInput.focus();
  }
});
</script>
</body>
</html>
HTML;

        file_put_contents("{$this->outputDir}/search.html", $html);
    }

    private function slug(string $text): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($text)));
    }
}
