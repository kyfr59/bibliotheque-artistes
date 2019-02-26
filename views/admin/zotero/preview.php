<link href="<?php echo WEB_DIR .'/themes/default/css/style.css' ?>" media="all" rel="stylesheet" type="text/css" >
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css" >
<style>
html, body {
    padding:10px;
}
table {
    font-family:arial;
    font-size:12px;
    border-collapse: collapse;
    border:1px solid #aaa;
}
tr, td {
    padding:8px;
    border:1px solid #aaa;
}
</style>

<form action="#" method="post">
    <br />
    <input type="hidden" name="file" value="<?php echo $tmpFile ?>">
    <input type="hidden" name="import" value="ok">
    <input type="submit" value="Lancer l'import des notices">
    &nbsp;&nbsp;<button onclick="javascript:history.back();">Précédent</button>
</form>

<style>
tr.error {
    background:#c98787;
}
</style>

<?php

// Affichage des données
echo '<table border="1">';
foreach($entetes as $entete) {
    echo '<tr>';
    echo "<td bgcolor=#ccc>Ligne&nbsp;#</td>";
    foreach($entete as $e)
        echo "<td bgcolor=#ccc>$e</td>";
    echo '</tr>';
}

$i = 1;


foreach($lignes as $kk => $ligne) {

    echo '<tr';
    if ($ligne['itemTypeError']) echo ' class="error" title="Item Type incorrect" ';
    echo '>';
    echo '<td style="text-align:center;">'.$i.'</td>';
    foreach($ligne as $key => $l) {

        if (substr($key, 0, 1) != '_') {
            if (is_array($l)) {
                echo '<td style="position:relative">';
                foreach($l as $k => $value) {
                    if($value)
                        echo '* '.$value.'<br>';
                }
                echo '<font color=red style="display:block; background:#ccc; font-size:9px; color:#555; padding:3px;position:absolute;bottom:1px;right:1px;">'.@$ligne['_'.$key].'<font>';
                echo '</td>';
            } else {
                echo '<td style="position:relative">';
                echo $l;
                if (@$ligne['_'.$key])
                    echo '<font color=red style="display:block; background:#ccc; font-size:9px; color:#555; padding:3px;position:absolute;bottom:1px;right:1px;">'.@$ligne['_'.$key].'<font>';
                echo "</td>";
            }
        }
    }
    echo '</tr>';
    $i++;

}
echo '</table>';
?>

<form action="#" method="post">
    <br />
    <input type="hidden" name="file" value="<?php echo $tmpFile ?>">
    <input type="hidden" name="import" value="ok">
    <input type="submit" value="Lancer l'import des notices">
    &nbsp;&nbsp;<button onclick="javascript:history.back();">Précédent</button>
</form>


