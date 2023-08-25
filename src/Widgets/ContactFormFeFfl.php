<?php

/* 
 * @package   [sap-bundle]
 * @author    Taheri Create Core Team
 * @license   GNU/LGPL
 * @copyright Taheri Create 2023 - 2026
 */

namespace Tahericreate\SapBundle\Widgets;

use Tahericreate\SapBundle\Widgets\StringUtil;
use Tahericreate\SapBundle\Widgets\Widget;

/**
 * Provide methods to handle select menus.
 *
 * @property boolean $mandatory
 * @property integer $size
 * @property boolean $multiple
 * @property array   $options
 * @property array   $unknownOption
 * @property boolean $chosen
 */
class ContactFormFeFfl extends \Widget
{
	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'contact_form_feffl';

	/**
	 * @param array $arrAttributes
	 */
	public function __construct($arrAttributes=null)
	{
		parent::__construct($arrAttributes);

		$this->preserveTags = true;
		$this->decodeEntities = true;
	}
	
	/**
	 * Return a parameter
	 *
	 * @param string $strKey The parameter key
	 *
	 * @return mixed The parameter value
	 */
	public function __get($strKey)
	{
		if ($strKey == 'options')
		{
			return $this->arrOptions;
		}

		return parent::__get($strKey);
	}

	/**
	 * Add specific attributes
	 *
	 * @param string $strKey   The attribute name
	 * @param mixed  $varValue The attribute value
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
			case 'options':
				$this->arrOptions = \StringUtil::deserialize($varValue);
				break;

			case 'rgxp':
			case 'minlength':
			case 'maxlength':
				// Ignore
				break;

			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}

	/**
	 * Check for a valid option (see #4383)
	 */
	public function validate()
	{
		$varValue = $this->getOptions(\Input::post($this->strName), true);
		if (!is_array($varValue) || empty($varValue)){
			$this->addError($GLOBALS['TL_LANG']['ERR']['invalid']);
		}
		parent::validate();
		
	}

	/**
	 * Check whether an input is one of the given options
	 *
	 * @param mixed $varInput The input string or array
	 *
	 * @return boolean True if the selected option exists
	 */
	protected function isValidOption($varInput)
	{
		$arrOptions = $this->arrOptions;

		if (isset($this->unknownOption[0]))
		{
			$this->arrOptions['unknown'][] = array('value'=>$this->unknownOption[0]);
		}
		
		$blnIsValid = parent::isValidOption($varInput);
		$this->arrOptions = $arrOptions;

		return $blnIsValid;
	}

	/**
	 * Generate the widget and return it as string
	 *
	 * @return string
	 */
	public function generate()
	{
		$arrOptions = array();
		$strClass = 'tl_select';

		if ($this->multiple)
		{
			$this->strName .= '[]';
			$strClass = 'tl_mselect';
		}

		$arrayCountries = \System::getCountries();
		$arrAllOptions = [];
		$arrAllOptions[] = [			
			'value'	=> '',
			'label'	=> '----'
		];
		foreach($arrayCountries as $countryKey => $countryName){
			$arrAllOptions[] = [
				'value'	=> strtoupper($countryKey),
				'label'	=> $countryName
			];
		}
		
		// Add an unknown option, so it is not lost when saving the record (see #920)
		if (isset($this->unknownOption[0]))
		{
			$arrAllOptions[] = array('value' => $this->unknownOption[0], 'label' => sprintf($GLOBALS['TL_LANG']['MSC']['unknownOption'], $this->unknownOption[0]));
		}

		foreach ($arrAllOptions as $strKey=>$arrOption)
		{
			if (isset($arrOption['value']))
			{
				$arrOptions[] = sprintf(
					'<option value="%s"%s>%s</option>',
					\StringUtil::specialchars($arrOption['value']),
					$this->isSelected($arrOption),
					$arrOption['label'] ?? null
				);
			}
			else
			{
				$arrOptgroups = array();

				foreach ($arrOption as $arrOptgroup)
				{
					$arrOptgroups[] = sprintf(
						'<option value="%s"%s>%s</option>',
						\StringUtil::specialchars($arrOptgroup['value'] ?? ''),
						$this->isSelected($arrOptgroup),
						$arrOptgroup['label'] ?? null
					);
				}

				$arrOptions[] = sprintf('<optgroup label="&nbsp;%s">%s</optgroup>', StringUtil::specialchars($strKey), implode('', $arrOptgroups));
			}
		}

		// Chosen
		if ($this->chosen)
		{
			$strClass .= ' tl_chosen';
		}

		return sprintf(
			'%s<select name="%s" id="ctrl_%s" class="%s%s"%s onfocus="Backend.getScrollOffset()">%s</select>%s',
			($this->multiple ? '<input type="hidden" name="' . (substr($this->strName, -2) == '[]' ? substr($this->strName, 0, -2) : $this->strName) . '" value="">' : ''),
			$this->strName,
			$this->strId,
			$strClass,
			($this->strClass ? ' ' . $this->strClass : ''),
			$this->getAttributes(),
			implode('', $arrOptions),
			$this->wizard
		);
	}
	
	/**
	 * Generate the options
	 *
	 * @return array The options array
	 */
	public function getOptions($selected = '--'){
		$arrayCountries = \System::getCountries();
		$arrAllOptions = [];
		foreach($arrayCountries as $countryKey => $countryName){
			$arrAllOptions[] = [				
				'type'     => 'option',
				'value'    => strtoupper($countryKey) . ' - ' . $countryName,
				'selected' => ($countryKey == $selected ? true : false),
				'label'    => strtoupper($countryKey) . ' - ' . $countryName,
			];
		}
		return $arrAllOptions;
	}
}

class_alias(ContactFormFeFfl::class, 'ContactFormFeFfl');
