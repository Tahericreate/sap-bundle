<?php

/* 
 * @package   [sap-bundle]
 * @author    Taheri Create Core Team
 * @license   GNU/LGPL
 * @copyright Taheri Create 2023 - 2026
 */

namespace Tahericreate\SapBundle\Model;

/*
 * Class CrmProductsModel
 *
 * @author Taheri Create Core Team
 */
class CrmProductsModel extends \Model
{
	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_crm_products';
}

class_alias(CrmProductsModel::class, 'CrmProductsModel');
