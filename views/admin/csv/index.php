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
    <li>Téléchargez le <a target="_blank" href="<?php echo BIBLIOTHEQUEARTISTES_PLUGIN_WEB ?>modele.xlsx">fichier modèle</a> sur votre ordinateur</li>
    <li>Ouvrez-le avec Microsoft Excel</li>
    <li>Remplissez-le en respectant les entêtes de colonnes</li>
    <li>La première ligne est à supprimer, elle est donnée à titre d'exemple, mais la ligne contenant les entêtes doit être conservée</li>
    <li>Une fois le fichier dûment rempli, choisissez <strong>"Enregister sous"</strong>, puis <strong>"Autres formats"</strong> - voir <a target="_blank" href="<?php echo BIBLIOTHEQUEARTISTES_PLUGIN_WEB . '/images/autres-formats.png'; ?>">copie d'écran</a></li>
    <li>Dans la boite de dialogue d'enregistrement, choisissez <strong>CSV (séparateur virgule)</strong> dans la zone <strong>Type</strong> - voir <a target="_blank" href="<?php echo BIBLIOTHEQUEARTISTES_PLUGIN_WEB . '/images/enregistrer-au-format-csv.png'; ?>">copie d'écran</a></li>
    <li>Cliquez sur le bouton "Choisissez un fichier" ci-dessous</li>
    <li>Sélectionnez votre fichier CSV</li>
    <li>Sélectionnez la collection dans laquelle importer les données (celle-ci doit être définie comme publique dans Omeka)</li>
    <li>Cliquez sur le bouton "Vérifier les données" ci-dessous</li>
    <li>Une page vous présente une prévisualisation des données de votre fichier (les données récupérées depuis la BnF sont présentées dans un sous-tableau)</li>
    <li><b style="color:orangered;">Vérifiez que la prévisualisation ne présente pas de problèmes d'accentuation ou d'apostrophes</b>, si c'est le cas vérifiez que le fichier CSV est bien <b>encodé en UTF-8</b></li>
</ul>

<br /><br />
<form action="#" method="post"  enctype="multipart/form-data" accept-charset="utf-8">
    <p>Choisissez la collection dans laquelle ajouter les notices du fichier CSV :
        <select id="collection" name="collection_id">
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

