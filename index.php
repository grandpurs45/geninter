<?php
declare(strict_types=1);

date_default_timezone_set('Europe/Paris');

function field(string $name, string $default = ''): string
{
    $value = $_POST[$name] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function rowsFromPost(): array
{
    $centres = $_POST['centre'] ?? [''];
    $engins = $_POST['engin'] ?? [''];
    $rows = [];

    foreach ($centres as $index => $centre) {
        $centre = is_string($centre) ? trim($centre) : '';
        $engin = isset($engins[$index]) && is_string($engins[$index]) ? trim($engins[$index]) : '';

        if ($centre !== '' || $engin !== '') {
            $rows[] = ['centre' => $centre, 'engin' => $engin];
        }
    }

    return $rows !== [] ? $rows : [['centre' => '', 'engin' => '']];
}

function firstCentre(array $moyens): string
{
    $centre = strtoupper(trim($moyens[0]['centre'] ?? ''));
    return preg_replace('/[^A-Z0-9_]/', '', $centre) ?: 'CENTRE';
}

function generatedOrderNumber(string $dateDepart, string $codeAppairage, array $moyens): string
{
    $centre = firstCentre($moyens);
    $seed = sprintf('%u', crc32($dateDepart . $codeAppairage . $centre));
    $number = str_pad((string) (((int) $seed % 900000) + 100000), 6, '0', STR_PAD_LEFT);

    return 'N° ' . $number . '-01-' . $centre;
}

function formatDateTime(string $value): string
{
    if ($value === '') {
        return '';
    }

    try {
        return (new DateTimeImmutable($value))->format('d/m/Y H:i:s');
    } catch (Exception) {
        return $value;
    }
}

$now = new DateTimeImmutable();
$callDate = $now->modify('+3 minutes');
$moyens = rowsFromPost();

$data = [
    'date_depart' => field('date_depart', $now->format('Y-m-d\TH:i')),
    'vehicule' => field('vehicule'),
    'code_appairage' => field('code_appairage'),
    'rang' => field('rang'),
    'sinistre' => field('sinistre'),
    'commune' => field('commune'),
    'plan' => field('plan'),
    'coord' => field('coord'),
    'quartier' => field('quartier'),
    'voie' => field('voie'),
    'contact' => field('contact'),
    'date_appel' => field('date_appel', $callDate->format('Y-m-d\TH:i')),
    'observations' => field('observations'),
    'bi_pi' => field('bi_pi'),
];

$data['numero'] = generatedOrderNumber($data['date_depart'], $data['code_appairage'], $moyens);
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generateur de fiche d'intervention</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>
    <main class="app-shell">
        <section class="editor" aria-label="Parametres de la fiche">
            <div class="editor__header">
                <div>
                    <h1>Generateur de fiche d'intervention</h1>
                    <p>Remplissez les champs, puis imprimez l'aperçu en PDF ou sur papier.</p>
                </div>
                <button type="button" class="primary-action" onclick="window.print()">Imprimer / PDF</button>
            </div>

            <form method="post" id="intervention-form">
                <div class="field-grid">
                    <label>
                        Date depart
                        <input type="datetime-local" name="date_depart" value="<?= e($data['date_depart']) ?>">
                    </label>
                    <label>
                        Vehicule principal
                        <input name="vehicule" value="<?= e($data['vehicule']) ?>">
                    </label>
                    <label>
                        Code appairage
                        <input name="code_appairage" value="<?= e($data['code_appairage']) ?>">
                    </label>
                    <label>
                        Rang
                        <input name="rang" value="<?= e($data['rang']) ?>">
                    </label>
                </div>
                <p class="generated-number">Numero d'ordre genere : <b><?= e($data['numero']) ?></b></p>

                <label>
                    Sinistre
                    <input name="sinistre" value="<?= e($data['sinistre']) ?>">
                </label>

                <div class="field-grid">
                    <label>
                        Commune
                        <input name="commune" value="<?= e($data['commune']) ?>">
                    </label>
                    <label>
                        N° Plan
                        <input name="plan" value="<?= e($data['plan']) ?>">
                    </label>
                    <label>
                        Coord
                        <input name="coord" value="<?= e($data['coord']) ?>">
                    </label>
                    <label>
                        Quartier
                        <input name="quartier" value="<?= e($data['quartier']) ?>">
                    </label>
                    <label>
                        Voie
                        <input name="voie" value="<?= e($data['voie']) ?>">
                    </label>
                    <label>
                        Contact
                        <input name="contact" value="<?= e($data['contact']) ?>">
                    </label>
                    <label>
                        Date d'appel
                        <input type="datetime-local" name="date_appel" value="<?= e($data['date_appel']) ?>">
                    </label>
                </div>

                <label>
                    Observations
                    <textarea name="observations" rows="3"><?= e($data['observations']) ?></textarea>
                </label>
                <label>
                    BI/PI dispo.
                    <input name="bi_pi" value="<?= e($data['bi_pi']) ?>">
                </label>

                <div class="section-title">
                    <h2>Moyens alertes</h2>
                    <button type="button" class="secondary-action" id="add-row">Ajouter un moyen</button>
                </div>
                <div id="moyens-list" class="moyens-list">
                    <?php foreach ($moyens as $row): ?>
                        <div class="moyen-row">
                            <label>
                                Centre
                                <input name="centre[]" value="<?= e($row['centre']) ?>">
                            </label>
                            <label>
                                Engin(s)
                                <textarea name="engin[]" rows="2"><?= e($row['engin']) ?></textarea>
                            </label>
                            <button type="button" class="icon-action" data-remove-row aria-label="Supprimer ce moyen">×</button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="primary-action">Mettre a jour l'aperçu</button>
                    <button type="button" class="secondary-action" onclick="window.print()">Imprimer / PDF</button>
                </div>
            </form>
        </section>

        <section class="preview-pane" aria-label="Apercu imprimable">
            <article class="sheet">
                <header class="sheet-header">
                    <div class="sheet-header__left">
                        <h2>DÉPART STANDARD</h2>
                        <p><?= e(formatDateTime($data['date_depart'])) ?></p>
                    </div>
                    <div class="stars" aria-hidden="true"><span>*</span><span>*</span><span>*</span></div>
                    <div class="sheet-header__right">
                        <strong><?= e($data['vehicule']) ?></strong>
                        <p>Code appairage : <b><?= e($data['code_appairage']) ?></b></p>
                    </div>
                </header>

                <p class="ordre-numero"><?= e($data['numero']) ?></p>
                <p class="rang">Rang : <?= e($data['rang']) ?></p>

                <div class="line-block">
                    <p><b>Sinistre</b><span>:</span><?= e($data['sinistre']) ?></p>
                </div>

                <h3>LOCALISATION DU SINISTRE</h3>
                <dl class="info-list">
                    <div class="commune-line">
                        <dt>Commune</dt>
                        <dd>: <?= e($data['commune']) ?></dd>
                        <dt>N° Plan</dt>
                        <dd>: <?= e($data['plan']) ?></dd>
                        <dt>Coord</dt>
                        <dd>: <?= e($data['coord']) ?></dd>
                    </div>
                    <div><dt>Quartier</dt><dd>: <?= e($data['quartier']) ?></dd></div>
                    <div><dt>Voie</dt><dd>: <?= e($data['voie']) ?></dd></div>
                    <div><dt>Contact</dt><dd>: <?= e($data['contact']) ?></dd></div>
                    <div><dt>Date d'appel</dt><dd>: <?= e(formatDateTime($data['date_appel'])) ?></dd></div>
                    <div><dt>Observations</dt><dd>: <?= nl2br(e($data['observations'])) ?></dd></div>
                </dl>

                <?php if ($data['bi_pi'] !== ''): ?>
                    <div class="line-block hydrants">
                        <p><b>BI/PI dispo.</b><span>:</span><?= e($data['bi_pi']) ?></p>
                    </div>
                <?php endif; ?>

                <h3>MOYENS ALERTES POUR CET ORDRE DE DEPART</h3>
                <table class="means-table">
                    <thead>
                        <tr>
                            <th>CENTRE</th>
                            <th>ENGIN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($moyens as $row): ?>
                            <tr>
                                <td><?= e($row['centre']) ?></td>
                                <td><?= nl2br(e($row['engin'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <footer class="sheet-footer">
                    <div class="stars" aria-hidden="true"><span>*</span><span>*</span><span>*</span></div>
                    <span>1/1</span>
                </footer>
            </article>
        </section>
    </main>

    <script src="public/app.js"></script>
</body>
</html>
