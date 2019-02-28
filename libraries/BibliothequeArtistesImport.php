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



	public function importFromCsv($lignes)
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

			$itemType = 19; // "Comparison item"

			$tags = array();

			//echo '<pre>';
			//print_r($ligne);

/*
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
*/
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

