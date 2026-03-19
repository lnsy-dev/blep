<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($topicName) ?> - <?= htmlspecialchars($siteTitle) ?></title>
<?= $styles ?>
</head>
<body>
<nav><a href="index.html">← index</a> | <a href="search.html">search</a> | <a href="changelog.html">changelog</a></nav>
<h1><?= htmlspecialchars($topicName) ?></h1>
<?= $content ?>
<footer>generated <?= htmlspecialchars($timestamp) ?></footer>
</body>
</html>
