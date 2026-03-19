<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Changelog - <?= htmlspecialchars($siteTitle) ?></title>
<?= $styles ?>
</head>
<body>
<nav><a href="index.html">← index</a> | <a href="search.html">search</a></nav>
<h1>changelog</h1>
<p class="subtitle">recent changes to business logic documentation</p>
<?= $content ?>
<footer>generated <?= htmlspecialchars($timestamp) ?></footer>
</body>
</html>
