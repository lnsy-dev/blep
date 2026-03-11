<?php

namespace Blep\Generator;

class HtmlGenerator
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
<nav><a href="search.html">search</a> | <a href="changelog.html">changelog</a></nav>
<h1>{$this->siteTitle}</h1>
<ul>
$links</ul>
<footer>generated {$this->timestamp}</footer>
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
{$this->getStyles()}
</head>
<body>
<nav><a href="index.html">← index</a> | <a href="search.html">search</a> | <a href="changelog.html">changelog</a></nav>
<h1>$topic</h1>
$content
<footer>generated {$this->timestamp}</footer>
</body>
</html>
HTML;

        file_put_contents("{$this->outputDir}/topic-$slug.html", $html);
    }

    private function getStyles(): string
    {
        return <<<CSS
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
html { height: 100%; }
body { font-family: 'Courier New', monospace; line-height: 1.6; color: #000; background: #fff; max-width: 900px; margin: 0 auto; padding: 20px; min-height: 100vh; display: flex; flex-direction: column; }
body > *:not(footer) { flex-shrink: 0; }
h1 { font-size: 1.5rem; margin-bottom: 20px; font-weight: normal; border-bottom: 2px solid #ccc; padding-bottom: 10px; }
h2 { font-size: 1.2rem; margin: 30px 0 10px 0; font-weight: normal; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
ul { list-style: none; padding-left: 20px; }
li { margin: 8px 0; }
li::before { content: "- "; }
a { color: #000; text-decoration: underline; }
a:hover { background: #000; color: #fff; }
.unresolved { text-decoration: line-through; }
.src-location { font-size: 0.9em; margin: 8px 0; }
.subtitle { font-size: 0.9em; margin-top: -10px; margin-bottom: 20px; }
details { margin: 10px 0; }
details summary { cursor: pointer; text-decoration: underline; }
details.code-block { border: 1px solid #ccc; padding: 10px; margin: 15px 0; }
details.history-block { border: 1px solid #ccc; padding: 10px; margin: 10px 0; font-size: 0.9em; }
details.history-block ul { padding-left: 0; }
details.history-block li::before { content: ""; }
details.rationale-block { display: inline-block; margin-left: 8px; font-size: 0.9em; }
details.rationale-block p { margin: 8px 0; padding: 8px; border: 1px solid #ccc; }
pre { background: #f5f5f5; border: 1px solid #ccc; padding: 15px; overflow-x: auto; margin: 10px 0; }
code { font-family: 'Courier New', monospace; }
nav { margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #ccc; }
footer { margin-top: auto; padding-top: 20px; border-top: 2px solid #ccc; }
.changelog-item { border-left: 2px solid #ccc; padding-left: 15px; margin-bottom: 30px; }
.changelog-meta { font-size: 0.9em; margin-bottom: 5px; }
.changelog-date { margin-right: 10px; }
.changelog-hash { margin-right: 10px; }
.changelog-topic { margin: 5px 0; }
.changelog-detail { margin: 5px 0; }
.changelog-message { font-size: 0.9em; margin-top: 5px; }
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
<nav><a href="index.html">← index</a> | <a href="search.html">search</a></nav>
<h1>changelog</h1>
<p class="subtitle">recent changes to business logic documentation</p>
$content
<footer>generated {$this->timestamp}</footer>
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
* { margin: 0; padding: 0; box-sizing: border-box; }
html { height: 100%; }
body { font-family: 'Courier New', monospace; line-height: 1.6; color: #000; background: #fff; max-width: 900px; margin: 0 auto; padding: 20px; min-height: 100vh; display: flex; flex-direction: column; }
body > *:not(footer) { flex-shrink: 0; }
h1 { font-size: 1.5rem; margin-bottom: 20px; font-weight: normal; border-bottom: 2px solid #ccc; padding-bottom: 10px; }
nav { margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #ccc; }
a { color: #000; text-decoration: underline; }
a:hover { background: #000; color: #fff; }
footer { margin-top: auto; padding-top: 20px; border-top: 2px solid #ccc; }
#search-input { width: 100%; padding: 10px; font-size: 1rem; font-family: 'Courier New', monospace; border: 2px solid #ccc; margin-bottom: 20px; }
#search-input:focus { outline: none; }
.search-card { border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; }
.topic-badge { display: inline-block; background: #ccc; color: #000; padding: 2px 8px; margin-right: 8px; }
.subtopic-title { margin: 8px 0; }
.detail-text { margin: 8px 0; }
.code-preview { background: #f5f5f5; border: 1px solid #ccc; padding: 10px; margin: 8px 0; font-size: 0.9em; overflow-x: auto; max-height: 150px; overflow-y: auto; }
.meta-info { font-size: 0.9em; margin-top: 8px; }
.view-link { display: inline-block; margin-top: 8px; }
.empty-state { text-align: center; padding: 40px; }
mark { background: #ccc; color: #000; padding: 2px 4px; }
</style>
</head>
<body>
<nav><a href="index.html">← index</a> | <a href="changelog.html">changelog</a></nav>
<h1>search documentation</h1>
<input type="text" id="search-input" placeholder="search topics, details, code, authors... (press / to focus)" autofocus>
<div id="results"></div>
<footer>powered by fuse.js</footer>

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
    resultsDiv.innerHTML = '<div class="empty-state">no results found. try different keywords.</div>';
    return;
  }

  resultsDiv.innerHTML = results.map(result => {
    const item = result.item;
    const link = `topic-${item.topicSlug}.html#${item.subtopicSlug}`;
    
    let detailText = item.detail;
    let codePreview = item.code ? item.code.split('\n').slice(0, 5).join('\n') : '';
    
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
        ${item.rationale ? `<div class="meta-info">why: ${escapeHtml(item.rationale)}</div>` : ''}
        ${codePreview ? `<div class="code-preview">${escapeHtml(codePreview)}</div>` : ''}
        <div class="meta-info">${item.file}:${item.line}${item.author ? ` • ${escapeHtml(item.author)}` : ''}</div>
        <a href="${link}" class="view-link">view in context →</a>
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
