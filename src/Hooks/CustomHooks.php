<?php

/* 
 * @package   [sap-bundle]
 * @author    Taheri Create Core Team
 * @license   GNU/LGPL
 * @copyright Taheri Create 2023 - 2026
 */


/**
 * Namespace
 */

namespace Tahericreate\SapBundle\Hooks;

use Tahericreate\SapBundle\Lib\ViLeadsCRMInjector;
use Tahericreate\SapBundle\Model\CrmProductsModel;

/**
 * Class CustomHooks
 */
class CustomHooks
{
	public function processFormData($submittedData, $formData, $files, $labels, $objForm)
	{
		// Initialize local vars
		$arrayLeadProducts = [];
		$arrayTemp = [];
		global $objPage;
	
		// Assorting products
		if (array_key_exists('inquiry', $submittedData) && $submittedData['inquiry']) {
			$arrayProducts = explode('|', $submittedData['inquiry']);
			foreach ($arrayProducts as $product) {
				$xT = trim(str_replace(':', '', strstr($product, ':')));
				$xA = explode(' ', $xT);
				$item = $xA[0];
				$quantity = $xA[2];
				$arrayTemp[] = $item;
				if (intval($item) && intval($quantity)) {
					$objCrmProductsModel = CrmProductsModel::findByExternal_id(intval($item));
					if ($objCrmProductsModel) {
						$arrayLeadProducts[] = [
							'ObjectID'	=> $objCrmProductsModel->object_id,
							'ProductID' => $objCrmProductsModel->product_id, //'40186200',
							'Quantity'	=> trim($quantity),
							'unitCode'	=> $objCrmProductsModel->base_uom
						];
					}
				}
			}
		}

		$objLeadsCrmInjector = new ViLeadsCRMInjector();
		// Obtain CSRF token from SAP C4
		$arrayCsrfToken = $objLeadsCrmInjector->getCSRFToken('products');
		$token = $arrayCsrfToken['x-csrf-token'][0];
		$cookie = $arrayCsrfToken['set-cookie'][1];

		//if($formData['id'] == 25){
		if (true) {
			// Create Library object
			$objLeadsCrmInjector = new ViLeadsCRMInjector();

			// Obtain CSRF token from SAP C4
			$arrayCsrfToken = $objLeadsCrmInjector->getCSRFToken();
			$token = $arrayCsrfToken['x-csrf-token'][0];
			$cookie = $arrayCsrfToken['set-cookie'][1];

			// Assort lead injection array
			$arrayLeads = [
				'Name'					=> strtoupper($formData['subject']) . ': ' . $submittedData[$objPage->language == 'de' ? 'anfragebetreff' : 'inquiry_subject'],
				'NameLanguageCode'		=> 'EN',
				'OriginTypeCode'		=> 'Z03',
				'Company'				=> $submittedData['company'],
				'ContactFirstName'		=> $submittedData['firstname'],
				'ContactLastName'		=> $submittedData['lastname'],
				'ContactEMail'			=> $submittedData['email']
			];

			// Assorting notes
			$notes = '';
			if ($submittedData['gender']) {
				$notes .= 'Salutation: ' . ucwords($submittedData['gender']) . "\n";
			}
			if ($submittedData['title']) {
				$notes .= 'Title: ' . ucwords($submittedData['title']) . "\n";
			}
			if ($submittedData['funktion']) {
				$notes .= 'Function: ' . ucwords($submittedData['funktion']) . "\n";
			}
			$notes .= 'Request: ' . $submittedData[$objPage->language == 'de' ? 'message' : 'dmessage'];
			$arrayLeads['Note'] = $notes;

			// Assorting category information
			if ($submittedData[$objPage->language == 'de' ? 'branche' : 'sector'] == 'industry') {
				$categoryCode = 'Z01';
			} else {
				$categoryCode = 'Z02';
			}
			if ($submittedData[$objPage->language == 'de' ? 'branche' : 'sector']) {
				$arrayLeads['GroupCode'] = $categoryCode;
			}

			if ($submittedData['ort']) {
				$arrayLeads['AccountCity'] = $submittedData['ort'];
			}
			if ($submittedData['street']) {
				$arrayLeads['AccountPostalAddressElementsStreetName'] = $submittedData['street'];
			}
			if ($submittedData[$objPage->language == 'de' ? 'land' : 'country']) {
				$arrayLeads['AccountCountry'] = strstr($submittedData[$objPage->language == 'de' ? 'land' : 'country'], ' ', true);
			}
			if ($submittedData['plz']) {
				$arrayLeads['AccountPostalAddressElementsStreetPostalCode'] = $submittedData['plz'];
			}
			if ($submittedData['phone']) {
				$arrayLeads['ContactPhone'] = $submittedData['phone'];
			}

			// Assorting products
			if (array_key_exists('inquiry', $submittedData) && $submittedData['inquiry']) {
				$arrayProducts = explode('|', $submittedData['inquiry']);
			}

			// Inject lead to CRM
			$dataLeadInsertion = $objLeadsCrmInjector->sendDataToCrm($token, $cookie, $arrayLeads);
			$leadDataRaw = strstr($dataLeadInsertion[1], 'etag', true);
			$leadDataRaw = strstr($leadDataRaw, 'location');
			$leadDataUrl = str_replace('location: ', '', $leadDataRaw);

			// Assort lead data
			foreach ($arrayLeadProducts as $arrayLeadProduct) {
				// Inject Product to lead
				$dataProductInsertion = $objLeadsCrmInjector->addProductsToLeads($token, $cookie, $arrayLeadProduct, $leadDataUrl);
			}

			// Inject Product to lead
			//$dataProductInsertion = $objLeadsCrmInjector->addProductsToLeads($token, $cookie, $arrayProductData, $leadDataUrl);
			//print_r($dataProductInsertion);
			//die('</pre><p>LeadDataURL:: ' . $leadDataUrl . '</p>');			
		}
	}
}
