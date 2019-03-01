<?php
class BibliothequeArtistesImport
{
	public static function getLastImportDate() {

		$items = get_db()->fetchAll('SELECT added FROM omeka_items WHERE collection_id = 1 ORDER by added DESC');
		if($items)
		if (strlen(trim($items[0]['added'])))
			return $items[0]['added'];
		return false;
	}

	public function import($lignes)
	{

		$color[BOOK]            		= 'orange';
		$color[BOOK_SECTION]    		= '#336699';
		$color[JOURNAL_ARTICLE] 		= 'green';
		$color[THESIS]          		= 'orangered';

		$fields[BOOK][4]                = 'DC:Title';
		$fields[BOOK][3]                = 'DC:Creator';
		$fields[BOOK][22]               = 'ITM:Collection';
		$fields[BOOK][60]               = 'ITM:Edition';
		$fields[BOOK][27]               = 'DC:Coverage';
		$fields[BOOK][26]               = 'DC:Publisher';
		$fields[BOOK][2]                = 'DC:Date';
		$fields[BOOK][35]               = 'ITM:Extra';
		$fields[BOOK][39]               = 'Tags';
		$fields[BOOK][9]                = 'DC:Source';

		$fields[JOURNAL_ARTICLE][4]     = 'DC:Title';
		$fields[JOURNAL_ARTICLE][3]     = 'DC:Creator';
		$fields[JOURNAL_ARTICLE][5]     = 'ITM:Publication';
		$fields[JOURNAL_ARTICLE][18]    = 'ITM:Volume';
		$fields[JOURNAL_ARTICLE][17]    = 'ITM:Number';
		$fields[JOURNAL_ARTICLE][15]    = 'ITM:Pages';
		$fields[JOURNAL_ARTICLE][2]     = 'DC:Date';
		$fields[JOURNAL_ARTICLE][35]    = 'ITM:Extra';
		$fields[JOURNAL_ARTICLE][39]    = 'Tags';
		$fields[JOURNAL_ARTICLE][9]     = 'DC:Source';

		$fields[BOOK_SECTION][4]        = 'DC:Title';
		$fields[BOOK_SECTION][3]        = 'DC:Creator';
		$fields[BOOK_SECTION][5]        = 'DC:Subject';
		$fields[BOOK_SECTION][18]       = 'ITM:Volume';
		$fields[BOOK_SECTION][26]       = 'ITM:Edition';
		$fields[BOOK_SECTION][27]       = 'DC:Coverage';
		$fields[BOOK_SECTION][41]       = 'DC:Publisher';
		$fields[BOOK_SECTION][2]        = 'DC:Date';
		$fields[BOOK_SECTION][15]       = 'ITM:Pages';
		$fields[BOOK_SECTION][35]       = 'ITM:Extra';
		$fields[BOOK_SECTION][39]       = 'Tags';
		$fields[BOOK_SECTION][9]        = 'DC:Source';

		$fields[THESIS][4]              = 'DC:Title';
		$fields[THESIS][3]              = 'DC:Creator';
		$fields[THESIS][26]             = 'ITM:University';
		$fields[THESIS][2]              = 'DC:Date';
		$fields[THESIS][35]             = 'ITM:Extra';
		$fields[THESIS][39]             = 'Tags';
		$fields[THESIS][9]              = 'DC:Source';

		$itemTypes[BOOK] 			= 20;
		$itemTypes[BOOK_SECTION] 	= 24;
		$itemTypes[JOURNAL_ARTICLE] = 25;
		$itemTypes[THESIS] 			= 26;

		$explodeFields = array(3,4,39,41);

		$nbLignes = 0;

		foreach($lignes as $noLigne => $ligne) {

			$elementTexts = array();

			$itemType = $ligne[1];

			$tags = array();

			if (!$ligne['itemTypeError']) {

				foreach($ligne as $noColonne => $values) {

					if (array_key_exists($noColonne, $fields[$itemType])) { // La clé existe dans le tableau de mapping)

						if ($noColonne == 39) { // Ce sont des tags

							foreach($values as $value) {
								if(strlen(trim($value)))
									$tags[] = $value;
							}

						} else {

							$map = explode(':', $fields[$itemType][$noColonne]);
							if (trim($map[0]) == 'DC')
								$set = 'Dublin Core';
							elseif (trim($map[0]) == 'ITM')
								$set = 'Item Type Metadata';
							else
								die($map[0] . " n'est pas un SET correct");

							$element = trim($map[1]);


							if (in_array($noColonne, $explodeFields)) { // Tableau de champs

								foreach ($values as $value)
									$elementTexts[$set][$element][] = array('text' => (string)trim($value), 'html' => false);

							} else { // Champ simple

								if (strlen(trim($values))) {

									$elementTexts[$set][$element][] = array('text' => (string)trim($values), 'html' => false);
								}
							}
						}
					}
				}
			}

			if (!$ligne['itemTypeError']) {

				$elementTexts['Item Type Metadata']['Imported from'][] = array('text' => 'Zotero', 'html' => false);

				$metadata['item_type_id']       = $itemTypes[$itemType];
				$metadata['collection_id']      = 1;
				$metadata['public']             = 1;
				$metadata['tags']               = $tags;

				$inserted_item = insert_item($metadata, $elementTexts);
			}

		}

	}



	public function importFromCsv($lignes, $collection_id)
	{

        $typeDeDonnee[0]    = null;
        $typeDeDonnee[1]    = "notice_iframe_same";
        $typeDeDonnee[2]    = "notice_iframe_different";
        $typeDeDonnee[3]    = "good_notice_bad_iframe";
        $typeDeDonnee[4]    = "not_notice_bad_iframe";

		$mapping[1]  		= "itm_Source number";
		$mapping[4]  		= "itm_Role";
        $mapping[5]         = 'Title';
        $mapping[6]  		= "itm_Number title";
		$mapping[7]  		= "itm_Tome";
		$mapping[8]  		= "itm_Volume";
		$mapping[9]  		= "itm_Number";
		$mapping[10] 		= "itm_Period";
		$mapping[11]        = 'Coverage';
		$mapping[12]        = 'Publisher';
		$mapping[13]        = 'Date';
		$mapping[14] 		= "itm_Impression";
		$mapping[15]        = 'Description';
		$mapping[16]        = 'Publisher';
		$mapping[17] 		= "itm_Print";
        $mapping[18]        = 'Publisher';
		$mapping[19]        = 'Publisher';
        $mapping[20]        = 'Relation';
        $mapping[21]        = 'Publisher';
		$mapping[22] 		= "itm_Artist annotation";
		$mapping[23] 		= "itm_Source comments";
        $mapping[24]        = 'Language';
        $mapping[25] 		= "itm_Documents type";
        $mapping[26]        = 'Type';
		$mapping[27] 		= "itm_Remarks";
		$mapping[28]        = 'Source';

        $mapping[1000]      = 'Creator';

        $prefix[18]         = "Edition";
        $prefix[12]         = "Editeur";
        $prefix[16]         = "Collection";
        $prefix[19]         = "Edition originale";
        $prefix[21]         = "Envoi";

        $dcFields  = array('Contributor', 'Coverage', 'Creator', 'Date', 'Description', 'Format', 'Identifier', 'Language', 'Publisher', 'Relation', 'Rights', 'Source', 'Subject', 'Title', 'Type', 'Abstract');

        define('XML_FILES', BASE_DIR.'/import/xml/');
        define('URL_CATALOGUE', 'Catalogue BnF');
        define('URL_GALLICA', 'Gallica');
        define('URL_ARCHIVES', 'Archives');


		foreach($lignes as $key => $ligne) {

			// Construction du nom complet
			$nom    = ucfirst(trim($ligne[2]));
        	$prenom = ucfirst(trim($ligne[3]));
	        if($nom || $prenom) {
	            if($nom)
	                $ligne[1000] = $nom;
	            if($prenom)
	                $ligne[1000] .= ', '. $prenom;
	        }

			foreach($ligne as $noColonne => $values) {

				$elementTexts = array();

				if (array_key_exists($noColonne, $mapping)) { // La clé existe dans le tableau de mapping)

		            // Récupère l'URL BnF dans le fichier
		            $urlBnF = $ligne[29];

		            if (strlen(trim($urlBnF)) && $ligne['29_parsed']) { // Gestion de l'import BnF

		            	// Détermine le type d'URL BnF
		                if (!strlen(trim($urlBnF))) {
		                    $urlType = null;
		                } elseif (strpos($urlBnF,"://gallica.bnf.fr/ark:/12148/")) {
		                	$urlType = URL_GALLICA;
		                } elseif (strpos($urlBnF,"://catalogue.bnf.fr/ark:/12148/")) {
		                	$urlType = URL_CATALOGUE;
		                } elseif (strpos($urlBnF,"://archive.org/details")) {
		                	$urlType = URL_ARCHIVES;
		                }

		                $metadatasBnf = $ligne['29_parsed'];

                        foreach ($metadatasBnf  as $tag => $value) {

                        	if (is_array($value)) {
                        		@$subValues = implode("#", $value);
                        		if (is_array($subValues)) {
                        			$text = implode("#", $subValues);
                        		} else {
                        			$text = $subValues;
                        		}
                        	} else {
                        		$text = $value;
                        	}
                        	$text = str_replace("#Array#", "#", $text);

                        	$texts = explode("#", $text);

                        	foreach($texts as $t) {
								if (in_array(ucfirst($tag), $dcFields)) { // Si c'est un champ DC
                                	$elementTexts['Item Type Metadata'][ucfirst($tag)][] = array('text' => ucfirst((string)trim($t)), 'html' => false);
                            	}
                            }
                        }

	                    if ($urlType == URL_ARCHIVES) { // Ajoute l'URL BnF au champ Identifier
	                        unset($elementTexts['Item Type Metadata']['Identifier']);
	                        $elementTexts['Item Type Metadata']['Identifier'][] = array('text' => (string)trim($urlBnF), 'html' => false);
	                    }

	                    $elementTexts['Item Type Metadata']['Imported from'][] = array('text' => (string)trim($urlType), 'html' => false);

		            } // Fin de l'import des données BnF


		            $urlVisionneuse = $ligne[30];

		            if (strlen(trim($urlVisionneuse))) { // Gestion de l'import de l'URL de la visionneuse

		                // Détermine le type d'URL Visionneuse
		                if (!strlen(trim($urlVisionneuse))) {
		                    $urlVisionneuseType = null;
		                } elseif (strpos($urlVisionneuse,"://gallica.bnf.fr/ark:/12148/")) {
		                	$urlVisionneuseType = URL_GALLICA;
		                } elseif (strpos($urlVisionneuse,"://catalogue.bnf.fr/ark:/12148/")) {
		                	$urlVisionneuseType = URL_CATALOGUE;
		                } elseif (strpos($urlVisionneuse,"://archive.org/details")) {
		                	$urlVisionneuseType = URL_ARCHIVES;
		                }

		                switch($urlVisionneuseType) {
		                    case URL_GALLICA :
		                        $visionneuse = trim($urlVisionneuse) . "/f1.image.mini";
		                        break;
		                    case URL_ARCHIVES :
		                        $visionneuse = trim($urlVisionneuse) . "?ui=embed";
		                        break;
		                    default:
		                        $visionneuse = null;
		                        break;
		                }

		                if ($visionneuse)
		                    $elementTexts['Item Type Metadata']['Viewer URL'][] = array('text' => $visionneuse, 'html' => false);
		            }

		            // Ajout du type de données à ITM
		            if ($typeDonnee = $ligne[31]) {
		            	$elementTexts['Item Type Metadata']['Comparison'][] = array('text' => $typeDeDonnee[$typeDonnee], 'html' => false);
		            }

		            // Gestion du Dublin Core
		            foreach($ligne as $colNumber => $values) {

		            	if ($colNumber == "29_parsed") continue;

		                if (strlen(trim($values))) {

		                   if (array_key_exists($colNumber, $mapping)) {

		                        if (array_key_exists($colNumber, $prefix))
		                            $values = $prefix[$colNumber] . ' : ' .$values;

		                        $texts = explode("#", $values);

								if (strpos($mapping[$colNumber], 'itm_') === 0) {
									$repo = "Item Type Metadata";
									$field = ltrim($mapping[$colNumber], 'itm_');
								} else {
									$repo = "Dublin Core";
									$field = $mapping[$colNumber];
								}

		                        foreach($texts as $t) {
		                        	$elementTexts[$repo][$field][] = array('text' => ucfirst($t), 'html' => false);
		                        }
		                    }
		                }
		            } // Fin de la boucle sur les colonnes


				}

			}

            $metadata['item_type_id']       = 19;
            $metadata['collection_id']      = $collection_id;
            $metadata['public']             = 1;

            //echo '<pre>';
            //print_r($elementTexts);

			$inserted_item = insert_item($metadata, $elementTexts);
		}
	}


	public static function callBnfOai($url, $repository = "catalogue") {

		$content = file_get_contents($url);
        $xml = simplexml_load_string($content);

        if ($repository == 'gallica') {

			if ($xml->notice->record) {
				$xmlMetadata = $xml->notice->record->metadata->children('oai_dc', TRUE)->children('dc', true);
			} else {
				return ['erreur' => 'Echec de la récupération des informations'];
			}

		} else {

			if ($xml->GetRecord->record) {
				$xmlMetadata = $xml->GetRecord->record->metadata->children('oai_dc', TRUE)->children('dc', true);
			} else {
				return ['erreur' => 'Echec de la récupération des informations'];
			}
		}

        return json_decode(json_encode((array)$xmlMetadata), TRUE);
	}

}

