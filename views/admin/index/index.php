<?php
queue_js_file('items-browse');
echo head(
    array(
        'title' => 'Importer des notices dans le système',
        'bodyclass' => 'items browse'
    )
);
?>

<ul>
    <li><a href="<?php echo admin_url('/bibliotheque-artistes/zotero'); ?>">Importer des notices Zotero</a></li>
    <li><a href="<?php echo admin_url('/bibliotheque-artistes/csv'); ?>">Importer des notices depuis un fichier CSV</a></li>
</ul>

<?php echo foot(); ?>

