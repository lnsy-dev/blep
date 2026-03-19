<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Search - <?= htmlspecialchars($siteTitle) ?></title>
<script src="https://cdn.jsdelivr.net/npm/fuse.js@7.0.0"></script>
<?= $styles ?>
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
