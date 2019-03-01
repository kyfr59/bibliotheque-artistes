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
    <input type="hidden" name="collection_id" value="<?php echo $_POST['collection_id'] ?>">
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
    if (@$ligne['itemTypeError']) echo ' class="error" title="Item Type incorrect" ';
    echo '>';
    echo '<td class="main" style="text-align:center;">'.($i+1).'</td>';

    foreach($ligne as $key => $l) {

        if (strpos($key, '_parsed')) {
            continue;
        }

        echo '<td style="position:relative">';

        if($key == 29 && !empty($l)) {

            echo '<table>';
            echo '<tr><td colspan="2"><strong><a class="bnf" href="'.$l.'" target="_blank">'.$l.'</a></td>';
            echo '<tr><td colspan="2"><strong>Informations collectées depuis le dépôt externe : </td>';
            $infosBnf = $lignes[$kk]['29_parsed'];
            foreach($infosBnf as $label => $infos) {
                $class = $label == "erreur" ? "erreur" : '';
                if (is_array($infos)) $infos = multi_implode($infos, " # ");
                echo '<tr class="'.$class.'"><td><strong>'.ucfirst($label).'</strong></td><td>'.$infos.'</td></tr>';
            }
            echo '</table>';

        } else {
            echo $l;
        }
        echo "</td>";
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

<style>
td.main {
   height: 150px;
}
a.bnf {
    color:#C76941;
    text-decoration: underline;
}
tr.erreur td {
    color:red;
}
</style>

<?php
function multi_implode($array, $glue) {
    $ret = '';

    foreach ($array as $item) {
        if (is_array($item)) {
            $ret .= multi_implode($item, $glue) . $glue;
        } else {
            $ret .= $item . $glue;
        }
    }

    $ret = substr($ret, 0, 0-strlen($glue));

    return $ret;
}

?>