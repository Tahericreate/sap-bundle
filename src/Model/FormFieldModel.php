<?php

/* 
 * @package   [sap-bundle]
 * @author    Taheri Create Core Team
 * @license   GNU/LGPL
 * @copyright Taheri Create 2023 - 2026
 */

namespace Tahericreate\SapBundle\Model;

/*
 * Class FormFieldModel
 *
 * @author Vrisini Core Team
 */
class FormFieldModel extends \Model
{
	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_form_field';
}

class_alias(FormFieldModel::class, 'FormFieldModel');
