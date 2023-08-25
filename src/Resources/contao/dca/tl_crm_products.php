<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/* 
* @package   [sap-bundle]
* @author    Taheri Create Core Team
* @license   GNU/LGPL
* @copyright Taheri Create 2023 - 2026
*/
/**
 * Table tl_crm_products
 */
$GLOBALS['TL_DCA']['tl_crm_products'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'switchToEdit'                => true,
		'enableVersioning'            => false,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		),
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('product_id'),
			'flag'                    => 1,
			'panelLayout'             => 'search,limit',			
			'disableGrouping'		  => false,
		),
		'label' => array
		(
			'fields'                => array('external_id', 'product_id', 'object_id', 'base_uom_text'),
			'format'                => '%s%s%s%s',
			'showColumns' 			=> true,
		),
		'global_operations' => array
		(			
			'all' => array
			(
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			),			
		),
		'operations' => array
		(
			// 'edit' => array
			// (
			// 	'href'                => 'table=tl_crm_products',
			// 	'icon'                => 'edit.gif',
			// 	'attributes'          => 'class="contextmenu"',
			// ),
			'editheader' => array
			(
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
				'attributes'          => 'class="edit-header"'
			),
			'copy' => array
			(
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			'delete' => array
			(
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
			),
			'show' => array
			(
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{general_legend},object_id,product_id,uuid,base_uom,base_uom_text,external_id'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'					=> "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'                   => "int(10) unsigned NOT NULL default 0"
		),
		'object_id'	=> array
        ( 
			'label'          		=> &$GLOBALS['TL_LANG']['tl_crm_products']['object_id'],
			'exclude'            	=> true,
			'search'              	=> true,
			'inputType'           	=> 'text',
			'eval'                	=> array('mandatory'=>false, 'tl_class'=>'w50'),
			'sql'					=> "varchar(100) NULL"
        ),
		'product_id' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_crm_products']['product_id'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "INT(16) NOT NULL DEFAULT 0"
		),		
		'uuid' => array
		(
			'label'                 => &$GLOBALS['TL_LANG']['tl_crm_products']['uuid'],
			'exclude'               => true,
			'inputType'           	=> 'text',
			'eval'                  => array('mandatory'=>false, 'tl_class'=>'w50'),
			'sql'                   => "VARCHAR(100) unsigned NOT NULL default ''"
		),
		'base_uom'	=> array
        ( 
			'label'          		=> &$GLOBALS['TL_LANG']['tl_crm_products']['base_uom'],
			'exclude'            	=> true,
			'search'              	=> true,
			'inputType'           	=> 'text',
			'eval'                	=> array('mandatory'=>false, 'tl_class'=>'w50'),
			'sql'					=> "varchar(100) NULL"
        ),
        'base_uom_text'	=> array
        ( 
			'label'          		=> &$GLOBALS['TL_LANG']['tl_crm_products']['base_uom_text'],
			'exclude'            	=> true,
			'search'              	=> true,
			'inputType'           	=> 'text',
			'eval'                	=> array('mandatory'=>false, 'tl_class'=>'w50'),
			'sql'					=> "varchar(255) NULL"
        ),
        'external_id'	=> array
        ( 
			'label'          		=> &$GLOBALS['TL_LANG']['tl_crm_products']['external_id'],
			'exclude'            	=> true,
			'search'              	=> true,
			'inputType'           	=> 'text',
			'eval'                	=> array('mandatory'=>false, 'tl_class'=>'w50'),
			'sql'					=> "varchar(100) NULL"
        ),
	)
);

?>
