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
namespace Tahericreate\SapBundle\BeMod;

use Tahericreate\SapBundle\Lib\ViLeadsCRMInjector;

/**
 * Class Imports
 *
 * @copyright  Taheri Create 2023 - 2026
 * @author     Taheri Create Core Team
 * @package    Devtools
 */
class ProductSync extends \BackendModule
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'product_sync';
	
	
	protected function compile(){
		// Handle purge
		if(\Input::post('customMode') == 'purge'){
			// Obtain BE user object
			$this->import('BackendUser', 'User');
			
			// Clear BE user session
			\Database::getInstance()->prepare("UPDATE tl_user SET session=NULL WHERE id=?")->execute($this->User->id);
			
			// Redirect for login
			$this->redirect('/contao');
		}
		
        // Initialize session and local vars
        $crmProductData = [];
        $reloadFlag = false;
        $refererCount = 1;
        $objSession = \Session::getInstance();
        
        // Fetch session data
		$arrayReloadCheckData = $objSession->get('crmProductData');
        
        $recordsPerPage = 1000;
        $redirectUrl = $_SERVER['REQUEST_URI'];
        
        // Create Library object
        $objLeadsCrmInjector = new ViLeadsCRMInjector();        
		
		// Get authentication token from CRM
		if(!$arrayReloadCheckData['continue']){
			if(\Input::post('import')){
				// Obtain CSRF token from SAP C4
				$arrayCsrfToken = $objLeadsCrmInjector->getCSRFToken();
				$token = $arrayCsrfToken['x-csrf-token'][0];
				$cookie = $arrayCsrfToken['set-cookie'][1];
							
				// Fetch products total count
				$arrayProductCountReturns = $objLeadsCrmInjector->getProductCount($token, $cookie);
				
				// Calculate total pages
				$totalCrmRecords = (int) $arrayProductCountReturns[3];
				$totalPages = ceil($totalCrmRecords / $recordsPerPage);
				
				// Truncate products table
				\Database::getInstance()->query("TRUNCATE tl_crm_products");
				
				// Set initial session
				$crmProductData = [
					'crmProductData' => [
						'totalCount'	=> $totalCrmRecords,
						'totalPages'	=> $totalPages,
						'currentPage'	=> 0,
						'totalImports'	=> 0,
						'token'			=> $token,
						'cookie'		=> $cookie,
						'continue'		=> true
					]
				];
				$objSession->setData($crmProductData);	
			}
		}
		else{			
			// Fetch session data
			$arraySessionData = $objSession->getData();
			$crmProductData = $arraySessionData['crmProductData'];			
			
			// Insert products from first sheet
			$arrayProductInserts = $objLeadsCrmInjector->syncProducts($crmProductData['token'], $crmProductData['cookie'], $crmProductData['currentPage']);
			
			// Update session
			if($crmProductData['currentPage'] < $crmProductData['totalPages']){
				$crmProductData['currentPage'] ++;
			}
			else{
				$crmProductData['currentPage'] = $crmProductData['totalPages'];
			}
			$crmProductData['totalImports'] += $arrayProductInserts[3];
			$objSession->setData(['crmProductData' => $crmProductData]);
		}
		
		$this->Template->crmProductData = $crmProductData;
		$this->Template->redirectUrl = $redirectUrl;
	}
}

class_alias(ProductSync::class, 'ProductSync');


