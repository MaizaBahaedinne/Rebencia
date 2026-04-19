<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($statusCode) ? $statusCode . ' – Erreur' : 'Erreur' ?> | Rebencia</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; padding: 2rem; }
        .container { max-width: 960px; margin: 0 auto; }
        .header {
            background: #1a3c5e; color: #fff;
            padding: 1.5rem 2rem; border-radius: 1rem 1rem 0 0;
            display: flex; align-items: center; gap: 1rem;
        }
        .status-code {
            font-size: 3rem; font-weight: 900; color: #e8a020; line-height: 1;
        }
        .header-text h1 { font-size: 1.25rem; font-weight: 700; }
        .header-text p  { font-size: .875rem; opacity: .75; margin-top: .25rem; }
        .body {
            background: #fff; padding: 2rem;
            border-radius: 0 0 1rem 1rem;
            box-shadow: 0 .5rem 2rem rgba(0,0,0,.08);
        }
        .exception-message {
            background: #fff3cd; border: 1px solid #ffc107;
            border-radius: .5rem; padding: 1rem 1.25rem;
            font-size: 1rem; color: #856404;
            margin-bottom: 1.5rem;
        }
        .trace-title { font-weight: 700; color: #495057; margin-bottom: .5rem; font-size: .875rem; text-transform: uppercase; letter-spacing: .05em; }
        pre {
            background: #1e293b; color: #e2e8f0;
            padding: 1.25rem; border-radius: .5rem;
            font-size: .78rem; overflow-x: auto;
            line-height: 1.6;
        }
        table { width: 100%; border-collapse: collapse; font-size: .85rem; margin-top: 1rem; }
        th { background: #f8f9fa; text-align: left; padding: .5rem .75rem; color: #6c757d; font-weight: 600; }
        td { padding: .4rem .75rem; border-bottom: 1px solid #f0f0f0; }
        td:first-child { width: 60px; color: #6c757d; }
        tr.highlight { background: #fff8e1; }
        .back-link {
            display: inline-block; margin-top: 1.5rem;
            color: #1a3c5e; text-decoration: none; font-weight: 600;
        }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="status-code"><?= $statusCode ?? 500 ?></div>
        <div class="header-text">
            <h1><?= esc(get_class($exception)) ?></h1>
            <p>
                <?= esc($exception->getFile()) ?> &nbsp;:&nbsp; ligne <?= $exception->getLine() ?>
            </p>
        </div>
    </div>

    <div class="body">
        <div class="exception-message">
            <?= esc($exception->getMessage()) ?>
        </div>

        <?php if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production') : ?>

        <!-- Stack Trace -->
        <div class="trace-title">Stack Trace</div>
        <pre><?php
            $trace = $exception->getTrace();
            foreach ($trace as $i => $frame) {
                $file = $frame['file'] ?? '[internal function]';
                $line = $frame['line'] ?? '';
                $class = $frame['class'] ?? '';
                $func  = $frame['function'] ?? '';
                $type  = $frame['type']  ?? '';
                echo "#$i  {$class}{$type}{$func}()\n    {$file}" . ($line ? ":{$line}" : '') . "\n";
            }
        ?></pre>

        <!-- Contexte fichier -->
        <?php if (is_file($exception->getFile())) :
            $lines   = file($exception->getFile());
            $errLine = $exception->getLine();
            $start   = max(0, $errLine - 6);
            $end     = min(count($lines), $errLine + 5);
        ?>
        <div class="trace-title" style="margin-top:1.5rem;">Contexte – <?= esc(basename($exception->getFile())) ?></div>
        <table>
            <thead><tr><th>Ligne</th><th>Code</th></tr></thead>
            <tbody>
            <?php for ($i = $start; $i < $end; $i++) : ?>
            <tr class="<?= ($i + 1) === $errLine ? 'highlight' : '' ?>">
                <td><?= $i + 1 ?></td>
                <td><pre style="background:transparent;color:inherit;padding:0;margin:0;"><?= esc($lines[$i]) ?></pre></td>
            </tr>
            <?php endfor; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php endif; ?>

        <a class="back-link" href="javascript:history.back()">← Retour en arrière</a>
    </div>
</div>
</body>
</html>
