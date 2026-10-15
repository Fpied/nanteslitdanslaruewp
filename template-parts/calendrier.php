<?php
// NOUVEAU : le mois affiché vient de l'URL (?mois=2026-10), sinon mois en cours
$moisActuel = new DateTime(current_time('Y-m-01'));
$moisMax    = (clone $moisActuel)->modify('+12 months');

$premier = $moisActuel;
if (isset($_GET['mois'])) {
    $demande = DateTime::createFromFormat('!Y-m-d', sanitize_text_field($_GET['mois']) . '-01');
    if ($demande && $demande <= $moisMax) {
        $premier = $demande;
    }
}

$precedent = (clone $premier)->modify('-1 month');
$suivant   = (clone $premier)->modify('+1 month');
// FIN NOUVEAU

$biblio       = get_option('nlr_biblio', []);
$lieuHabituel = get_option('nlr_biblio_lieu', '');
$nbJours  = (int) $premier->format('t');
$decalage = (int) $premier->format('N') - 1;

$evenementsMois = new WP_Query([
    'post_type'      => ['evenement', 'activite'],
    'posts_per_page' => -1,
    'meta_query'     => [[
        'key'     => 'date_evenement',
        'value'   => [$premier->format('Y-m-d'), $premier->format('Y-m-t')],
        'compare' => 'BETWEEN',
        'type'    => 'DATE',
    ]],
]);

$parJour = [];
while ($evenementsMois->have_posts()) : $evenementsMois->the_post();
    $date   = get_post_meta(get_the_ID(), 'date_evenement', true);
    $numero = (int) date('j', strtotime($date));
    $parJour[$numero][] = [
        'titre' => get_the_title(),
        'lien'  => get_permalink(),
        'type' => get_post_type(),
    ];
endwhile;
wp_reset_postdata();
?>

<section class="calendrier">
    <!-- NOUVEAU : le titre est entouré des deux liens -->
    <div class="calendrier__nav">
        <a href="<?php echo esc_url(add_query_arg('mois', $precedent->format('Y-m'))); ?>">‹ Précédent</a>
        <h2 class="calendrier__mois"><?php echo esc_html(date_i18n('F Y', $premier->getTimestamp())); ?></h2>
        <?php if ($suivant <= $moisMax) : ?>
            <a href="<?php echo esc_url(add_query_arg('mois', $suivant->format('Y-m'))); ?>">Suivant ›</a>
        <?php endif; ?>
    </div>
    <!-- FIN NOUVEAU -->

    <div class="calendrier__grille">
        <?php foreach (['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $nomJour) : ?>
    <div class="calendrier__entete"><?php echo $nomJour; ?></div>
<?php endforeach; ?>

<?php for ($i = 0; $i < $decalage; $i++) : ?>
    <div class="calendrier__vide"></div>
<?php endfor; ?>

<?php for ($jour = 1; $jour <= $nbJours; $jour++) : ?>
    <div class="calendrier__jour">
        <span class="calendrier__num"><?php echo $jour; ?></span>
        <?php
            $dateJour = sprintf('%s-%02d', $premier->format('Y-m'), $jour);
            $estJeudi = date('N', strtotime($dateJour)) == 4;
            $infos    = $biblio[$dateJour] ?? [];
            $lieuJour = !empty($infos['lieu']) ? $infos['lieu'] : $lieuHabituel;
            ?>
            <?php if ($estJeudi && empty($infos['annule'])) : ?>
                <span class="calendrier__evenement calendrier__evenement--biblio">
                    Bibliothèque de rue<?php if ($lieuJour) : ?> – <?php echo esc_html($lieuJour); ?><?php endif; ?>
                </span>
            <?php endif; ?>
        <?php if (isset($parJour[$jour])) : ?>
            <?php foreach ($parJour[$jour] as $evt) : ?>
                <a class="calendrier__evenement calendrier__evenement--<?php echo esc_attr($evt['type']); ?>" href="<?php echo esc_url($evt['lien']); ?>">
                    <?php echo esc_html($evt['titre']); ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php endfor; ?>
    </div>
</section>