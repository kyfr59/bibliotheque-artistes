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
class BibliothequeArtistes_CsvController extends Omeka_Controller_AbstractActionController
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

            if (!$file['tmp_name']) return;

            // Récupération du fichier CSV
            $path = fopen($file['tmp_name'], 'r');

            $i = 0;

            while (($line = fgetcsv($path, 0, ";")) !== FALSE) {

                $line = $this->prepareLigne($line);

                if (empty($line[2]) && empty($line[3]) && empty($line[4])) { // Passe si le nom, prénom et titre est vide
                    continue;
                }

                if ($i > 0 && !empty($line[29])) {

                    $url = trim($line[29]);
                    $ark = str_replace('https://catalogue.bnf.fr/ark:/12148/', '', $url);
                    $ark = str_replace('http://catalogue.bnf.fr/ark:/12148/', '', $ark);
                    $ark = str_replace('.public', '', $ark);
                    $ark = str_replace('/PUBLIC', '', $ark);
                    $ark = trim($ark);
                    $ark = rtrim($ark, '/');
                    $urlToCall = 'http://catoai.bnf.fr/oai2/OAIHandler?verb=GetRecord&metadataPrefix=oai_dc&identifier=oai:bnf.fr:catalogue/ark:/12148/'.$ark;
                    $line['29_parsed'] = BibliothequeArtistesImport::callBnfOai($urlToCall);
                }

                if ($i > 0 && !empty($line[30])) {

                    $url = trim($line[30]);

                    $ark = str_replace('http://gallica.bnf.fr/ark:/12148/', '', $url);
                    $ark = str_replace('https://gallica.bnf.fr/ark:/12148/', '', $ark);
                    $ark = str_replace('.image', '', $ark);
                    $urlToCall = 'http://gallica.bnf.fr/services/OAIRecord?ark=ark:/12148/'.$ark;
                    $line['30_parsed'] = BibliothequeArtistesImport::callBnfOai($urlToCall, 'gallica');
                }

                if($i > 0)
                    $lignes[] = $line;
                else
                    $entetes[] = $line;
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



    private function prepareLigne($line) {

        // Converti le format natif Excel en UTF-8
        foreach($line as $key => $l) {
            $line[$key] = iconv( "Windows-1252", "UTF-8", $l );
        }

        return $line;
    }

}
