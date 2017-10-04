<?php
/**
 * Bibliothèque d'Artistes
 *
 * @copyright Copyright 2015-2020 Limonade & Co (Paris)
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */


if (!defined('BIBLIOTHEQUEARTISTES_PLUGIN_DIR')) define('BIBLIOTHEQUEARTISTES_PLUGIN_DIR', dirname(__FILE__));
if (!defined('BIBLIOTHEQUEARTISTES_PLUGIN_WEB')) define('BIBLIOTHEQUEARTISTES_PLUGIN_WEB', WEB_PLUGIN . '/BibliothequeArtistes/');

/**
 * Bibliothèque d'Artistes plugin.
 */
class BibliothequeArtistesPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */

    // protected $_hooks = array();

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array('admin_navigation_main');

    /**
     * @var array Options and their default values.
     */

    /**
     * Add the Simple Pages link to the admin main navigation.
     *
     * @param array Navigation array.
     * @return array Filtered navigation array.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Bibliothèque d\'Artistes'),
            'uri' => url('bibliotheque-artistes'),
            'resource' => 'SimplePages_Index',
            'privilege' => 'browse'
        );
        return $nav;
    }
}
