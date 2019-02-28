<?php
queue_js_file('items-browse');
echo head(
    array(
        'title' => 'Importer des notices depuis un fichier CSV',
        'bodyclass' => 'items browse'
    )
);
?>

<p><b>Cette page vous permet d'importer des notices via un fichier CSV.</b></p>

<p>Procédure d'import en CSV : </p>

<ul>
    <li>Installez le <a target="_blank" href="https://www.zotero.org/download/">logiciel Zotero 5.0</a> pour votre système d'exploitation</li>
    <li>Configurez le logiciel pour gérer votre bibliothèque</li>
    <li>Cliquez droit sur votre bibliothèque et choisissez <strong>"Exporter la bibliothèque"</strong> - voir <a target="_blank" href="<?php echo BIBLIOTHEQUEARTISTES_PLUGIN_WEB . '/images/exporter.png'; ?>">copie d'écran</a></li>
    <li>Enregistrez le fichier au format CSV avec l'encodage <strong>"Unicode (UTF-8 sans BOM)"</strong> - voir <a target="_blank" href="<?php echo BIBLIOTHEQUEARTISTES_PLUGIN_WEB . '/images/enregistrer.png'; ?>">copie d'écran</a></li>
    <li>Cliquez sur le bouton "Choisissez un fichier" ci-dessous</li>
    <li>Sélectionnez votre fichier CSV</li>
    <li>Cliquez sur le bouton "Vérifier les données" ci-dessous</li>
    <li>Une page vous présente une prévisualisation des données de votre fichier</li>
    <li><b style="color:orangered;">Vérifiez que la prévisualisation ne présente pas de problèmes d'accentuation</b>, si c'est le cas vérifiez que le fichier CSV est bien <b>encodé en UTF-8</b></li>
</ul>

<?php
    echo "<h1 style='margin-top:30px; margin-bottom:-10px;'>Date du dernier import : ";
    $date = BibliothequeArtistesImport::getLastImportDate();
    if ($date)
        echo $date;
    else
        echo "aucun import réalisé";
    echo "</h1>";
?>

<br /><br />
<form action="#" method="post"  enctype="multipart/form-data" accept-charset="utf-8">
    <p>Choisissez la collection dans laquelle ajouter les notices du fichier CSV :
        <select id="collection" >
            <option value="">Faites votre choix</option>
            <?php foreach($collections as $collection): ?>
                <option value="<?php echo $collection->id ?>"><?php echo $collection->getProperty("display_title"); ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <input class="button" type="file" name="file">
    <br /><br /><br /><input id="submit" type="submit" value="Vérifier les données">
</form>
<script>
jQuery(document).ready(function($) {
    $("#submit").click(function () {
        val = $("#collection :selected").val();
        if (val.length === 0) {
            alert("Vous devez sélectionner une collection");
            return false;
        }
    });
});
</script>

<?php echo foot(); ?>

