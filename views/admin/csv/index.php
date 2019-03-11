<?php
queue_js_file('items-browse');
echo head(
    array(
        'title' => 'Importer des notices depuis un fichier CSV',
        'bodyclass' => 'items browse'
    )
);
?>

<?php echo flash(); ?>

<p><b>Cette page vous permet d'importer des notices via un fichier CSV.</b></p>

<p>Procédure d'import en CSV : </p>

<ul>
    <li>Téléchargez le <a target="_blank" href="<?php echo BIBLIOTHEQUEARTISTES_PLUGIN_WEB ?>modele.xlsx">fichier modèle</a> sur votre ordinateur</li>
    <li>Ouvrez-le avec Microsoft Excel</li>
    <li>Aidez-vous du <a target="_blank" href="<?php echo BIBLIOTHEQUEARTISTES_PLUGIN_WEB ?>guide.pdf">document explicatif</a> pour remplir le fichier Excel</li>
    <li>La première ligne est à supprimer, elle est donnée à titre d'exemple, mais la ligne contenant les entêtes doit être conservée</li>
    <li>Vous pouvez utiliser le séparateur <strong>#</strong> pour ajouter plusieurs valeurs dans le même champ, par exemple pour <strong>Français#Anglais</strong> pour ajouter 2 langues</li>
    <li>Il est conseiller de ne pas importer plus de 50 lignes (notices) lors du même import</li>
    <li>Une fois le fichier dûment rempli, choisissez <strong>"Enregister sous"</strong>, puis <strong>"Autres formats"</strong> - voir <a target="_blank" href="<?php echo BIBLIOTHEQUEARTISTES_PLUGIN_WEB . '/images/autres-formats.png'; ?>">copie d'écran</a></li>
    <li>Dans la boite de dialogue d'enregistrement, choisissez <strong>CSV (séparateur point-virgule)</strong> dans la zone <strong>Type</strong> - voir <a target="_blank" href="<?php echo BIBLIOTHEQUEARTISTES_PLUGIN_WEB . '/images/enregistrer-au-format-csv.png'; ?>">copie d'écran</a></li>
    <li>Sélectionnez, ci-dessous, la collection dans laquelle importer les données (celle-ci doit être définie comme publique dans Omeka)</li>
    <li>Cliquez sur le bouton "Parcourir" ci-dessous</li>
    <li>Sélectionnez votre fichier CSV</li>
    <li>Cliquez sur le bouton "Vérifier les données" ci-dessous</li>
    <li>Une page vous présente une prévisualisation des données de votre fichier (les données récupérées depuis la BnF sont présentées dans un sous-tableau)</li>
    <li><b style="color:orangered;">Vérifiez que la prévisualisation ne présente pas de problèmes d'accentuation ou d'apostrophes</b>, si c'est le cas vérifiez que le fichier CSV est bien encodé en UTF-8</li>
    <li>Sur cette fenêtre cliquer sur" Lancer l'import des notices".</li>
    <li>Si tout s'est bien passé, vous revenez à l'écran précédent avec une mention du nombre de lignes importées. Vérifiez qu'il y en a le même nombre que dans votre fichier.</li>
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

