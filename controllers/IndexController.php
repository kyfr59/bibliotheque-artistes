<?php
/**
* @copyright Copyright 2015-2020 Limonade & Co (Paris)
* @author Franck Dupont <kyfr59@gmail.com>
* @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
* @package BibliothequeArtistes
* @subpackage Controllers
*/

/**
 * Index controller
 *
 * @package BibliothequeArtistes
 * @subpackage Controllers
 */
class BibliothequeArtistes_IndexController extends Omeka_Controller_AbstractActionController
{
    /**
     * Initialization controller
     *
     * @return void
     */
    public function init()
    {
        $this->file = BIBLIOTHEQUEARTISTES_PLUGIN_DIR. '/temp/lignes.tmp';
        $this->_helper->db->setDefaultModelName('Item');
    }


    public function indexAction() {

        define('BOOK', 'Book');
        define('BOOK_SECTION', 'BookSection');
        define('JOURNAL_ARTICLE', 'JournalArticle');
        define('THESIS', 'Thesis');

        $color[BOOK]            = 'orange';
        $color[BOOK_SECTION]    = '#336699';
        $color[JOURNAL_ARTICLE] = 'green';
        $color[THESIS]          = 'orangered';

        $fields[BOOK][4]                = 'DC:Title';
        $fields[BOOK][3]                = 'DC:Creator';
        $fields[BOOK][22]               = 'Books:Collection';
        $fields[BOOK][60]               = 'Books:Edition';
        $fields[BOOK][27]               = 'DC:Coverage';
        $fields[BOOK][26]               = 'DC:Publisher';
        $fields[BOOK][2]                = 'DC:Date';
        $fields[BOOK][35]               = 'Books:Extra';
        $fields[BOOK][39]               = 'Tags';
        $fields[BOOK][9]                = 'DC:Source';

        $fields[JOURNAL_ARTICLE][4]     = 'DC:Title';
        $fields[JOURNAL_ARTICLE][3]     = 'DC:Creator';
        $fields[JOURNAL_ARTICLE][5]     = 'Publication';
        $fields[JOURNAL_ARTICLE][18]    = 'JournalArticle:Volume';
        $fields[JOURNAL_ARTICLE][17]    = 'JournalArticle:Numéro';
        $fields[JOURNAL_ARTICLE][15]    = 'JournalArticle:Pages';
        $fields[JOURNAL_ARTICLE][2]     = 'DC:Date';
        $fields[JOURNAL_ARTICLE][35]    = 'JournalArticle:Extra';
        $fields[JOURNAL_ARTICLE][39]    = 'Tags';
        $fields[JOURNAL_ARTICLE][9]     = 'DC:Source';

        $fields[BOOK_SECTION][4]        = 'DC:Title';
        $fields[BOOK_SECTION][3]        = 'DC:Creator';
        // $fields[BOOK_SECTION][5]        = 'DC:Subject';
        $fields[BOOK_SECTION][18]       = 'BookSection:Volume';
        $fields[BOOK_SECTION][26]       = 'BookSection:Edition';
        $fields[BOOK_SECTION][27]       = 'DC:Coverage';
        $fields[BOOK_SECTION][41]       = 'DC:Publisher';
        $fields[BOOK_SECTION][2]        = 'DC:Date';
        $fields[BOOK_SECTION][15]       = 'BookSection:Pages';
        $fields[BOOK_SECTION][35]       = 'BookSection:Extra';
        $fields[BOOK_SECTION][39]       = 'Tags';
        $fields[BOOK_SECTION][9]        = 'DC:Source';

        $fields[THESIS][4]              = 'DC:Title';
        $fields[THESIS][3]              = 'DC:Creator';
        $fields[THESIS][26]             = 'Thesis:Universite';
        $fields[THESIS][2]              = 'DC:Date';
        $fields[THESIS][35]             = 'Thesis:Extra';
        $fields[THESIS][39]             = 'Tags';
        $fields[THESIS][9]              = 'DC:Source';

        $explodeFields = array(3,4,39,41);

        if ($this->getRequest()->isPost() && isset($_POST['import'])  && $_POST['import'] == 'ok') {

            $lignes = unserialize(file_get_contents($this->file));

            $import = new BibliothequeArtistesImport();
            $import->import($lignes);
            unlink($this->file);

        } elseif ($this->getRequest()->isPost() && $file = $_FILES['file']) {

            $keys = array_unique(array_merge(array_keys($fields[BOOK]), array_keys($fields[JOURNAL_ARTICLE]), array_keys($fields[BOOK_SECTION]), array_keys($fields[THESIS])));

            if (!$file['tmp_name']) return;

            // Récupération du fichier CSV
            $path = fopen($file['tmp_name'], 'r');

            $i = 0;
            while (($line = fgetcsv($path, 0, ",")) !== FALSE) {

                $line = $this->prepareTitre($line);

                foreach($line as $key => $l) {

                    $itemType = ucfirst($line[1]);

                    if ($key != 1 && !in_array($key, $keys)) {

                        unset($line[$key]); // Delete line if not in $fields array.

                    } else {

                        if ($i > 0) {

                            $line['itemTypeError'] = false;

                            $l = trim($l);
                            $l = ucfirst($l);
                            $l = preg_replace('/^é/', 'E', $l, 1); // Replace é par E en début de phrase
                            $l = str_replace("\t", '', $l); // remove tabs
                            $l = str_replace("\n", '', $l); // remove new lines
                            $l = str_replace("\r", '', $l); // remove carriage returns

                            if (in_array($key, $explodeFields)) {
                                $l = explode(';', $l);
                                foreach($l as $kk => $v) {

                                    $v = trim($v);
                                    $v = ucfirst($v);
                                    $v = preg_replace('/^é/', 'E', $v, 1); // Replace é par E en début de phrase
                                    $v = str_replace("\t", '', $v); // remove tabs
                                    $v = str_replace("\n", '', $v); // remove new lines
                                    $v = str_replace("\r", '', $v); // remove carriage returns

                                    $l[$kk] = $v;
                                }
                            }

                            $line[$key] = $l;
                            if($key>1) {
                                if(isset($fields[$itemType][$key]))
                                    $line['_'.$key] = $fields[$itemType][$key];
                                else
                                    $line['_'.$key] = 'N/A';
                            }

                            if ($itemType != BOOK && $itemType != BOOK_SECTION && $itemType != JOURNAL_ARTICLE && $itemType != THESIS) $line['itemTypeError'] = true;
                        }

                    }
                }


                if($i > 0)
                    $lignes[] = $line;
                else
                    $entetes[] = $line;


                foreach($entetes as $key => $entete) {
                    if ($key != 1 && !in_array($key, $keys))
                        unset($line[$key]); // Delete line if not in $fields array.
                }

                $i++;
            }
            fclose($path);


            file_put_contents($this->file, serialize($lignes));

            $this->view->tmpFile = $file['tmp_name'];
            $this->view->entetes = $entetes;
            $this->view->lignes = $lignes;
            $this->render('preview');
        }
    }

    private function prepareTitre($ligne) {

        if(ucfirst($ligne[1]) == BOOK_SECTION) {
            if (strlen(trim($ligne[5]))) {
                $ligne[4] = 'Titre du livre : '.$ligne[5].';Titre du chapitre : '.$ligne[4];
                $ligne[5] = null;
            }
        }
        return $ligne;
    }
}
