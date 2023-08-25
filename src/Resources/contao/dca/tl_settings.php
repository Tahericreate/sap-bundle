<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/* 
 * @package   [sap-bundle]
 * @author    Taheri Create Core Team
 * @license   GNU/LGPL
 * @copyright Taheri Create 2023 - 2026
 */

 /**
 * Table tl_settings
 */

// Palettes

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace('{global_legend},adminEmail;', '{global_legend},adminEmail;{sap_legend},lead_collection_url,product_collection_url,sap_username,sap_password;',  $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']);

// Fields

$GLOBALS['TL_DCA']['tl_settings']['fields']['lead_collection_url'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['lead_collection_url'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => ['mandatory' => true,'tl_class'=>'w50'],
    'sql'                     => "VARCHAR(255) default NULL"
];
$GLOBALS['TL_DCA']['tl_settings']['fields']['product_collection_url'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['product_collection_url'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => ['mandatory' => true, 'tl_class' => 'w50'],
    'sql'                     => "VARCHAR(255) default NULL"
];
$GLOBALS['TL_DCA']['tl_settings']['fields']['sap_username'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sap_username'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => ['mandatory' => true, 'tl_class' => 'w50'],
    'sql'                     => "VARCHAR(255) default NULL"
];
$GLOBALS['TL_DCA']['tl_settings']['fields']['sap_password'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sap_password'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => ['mandatory' => false, 'tl_class' => 'w50'],
    'sql'                     => "VARCHAR(255) default NULL"
];
