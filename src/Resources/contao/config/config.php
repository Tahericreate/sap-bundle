<?php

use Tahericreate\SapBundle\Widgets\ContactFormFeFfl;
use Tahericreate\SapBundle\Hooks\CustomHooks;

/* 
 * @package   [sap-bundle]
 * @author    Taheri Create Core Team
 * @license   GNU/LGPL
 * @copyright Taheri Create 2023 - 2026
 */

// Front end form fields
$GLOBALS['TL_FFL']['country_list'] = ContactFormFeFfl::class;

/*
 * Backend Module
 */
$GLOBALS['BE_MOD']['crm'] = array(
	'tl_crm_products' => array
	(
		'tables' => array('tl_crm_products'),
	),
	'product_sync' => array
	(
		'callback' => 'Tahericreate\SapBundle\BeMod\ProductSync'
	),
);


/**
 * HOOKS
 */
$GLOBALS['TL_HOOKS']['processFormData'][] = array(CustomHooks::class, 'processFormData');
