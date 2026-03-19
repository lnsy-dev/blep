<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($siteTitle) ?></title>
<?= $styles ?>
</head>
<body>
<nav><a href="search.html">search</a> | <a href="changelog.html">changelog</a></nav>
<h1><?= htmlspecialchars($siteTitle) ?></h1>
<ul>
<?php foreach ($topics as $t): ?>
<li><a href="topic-<?= $t['slug'] ?>.html"><?= htmlspecialchars($t['name']) ?></a></li>
<?php endforeach; ?>
</ul>
<footer>generated <?= htmlspecialchars($timestamp) ?></footer>
</body>
</html>
