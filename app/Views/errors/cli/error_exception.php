CLI EXCEPTION: <?= isset($title) ? $title : get_class($exception) ?>

Message : <?= $exception->getMessage() ?>

File    : <?= $exception->getFile() ?>
Line    : <?= $exception->getLine() ?>

Stack Trace:
<?= $exception->getTraceAsString() ?>
