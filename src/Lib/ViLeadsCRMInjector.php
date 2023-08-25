<?php
/* 
 * @package   [sap-bundle]
 * @author    Taheri Create Core Team
 * @license   GNU/LGPL
 * @copyright Taheri Create 2023 - 2026
 */
namespace Tahericreate\SapBundle\Lib;

use Tahericreate\SapBundle\Model\CrmProductsModel;
/**
 * Provide methods to inject data to SAP C4.
 */
class ViLeadsCRMInjector
{   
	private $leadCollectionUrl;
	private $productCollectionUrl;
	private $username;
	private $password;
    
    public function __construct(){
		// Initialise SAP parameters
		$this->leadCollectionUrl = $GLOBALS['TL_CONFIG']['lead_collection_url'];
		$this->productCollectionUrl = $GLOBALS['TL_CONFIG']['product_collection_url'];
		$this->username = $GLOBALS['TL_CONFIG']['sap_username'];
		$this->password = $GLOBALS['TL_CONFIG']['sap_password'];
	}
    
    public function getCSRFToken($mode = 'leads') {
		// Initialize CURL
		if($mode == 'leads'){
			$ch = curl_init($this->leadCollectionUrl);
		}
		else{
			$ch = curl_init($this->productCollectionUrl);
		}
		
		$request_headers = array();
		$request_headers[] = 'X-CSRF-Token: Fetch';
		$request_headers[] = 'Content-Type: application/json';
		$request_headers[] = 'Accept: application/json';

		curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $header) use (&$headers){
			$len  = strlen($header);
			$header = explode(':', $header, 2);
			if (count($header) < 2) { // ignore invalid headers
				return $len;
			}

			$name = strtolower(trim($header[0]));
			if (is_array($headers) && !array_key_exists($name, $headers)) {
				$headers[$name] = [trim($header[1])];
			} 
			else {
				$headers[$name][] = trim($header[1]);
			}
			return $len;
		});

		$tmpfname = '/tmp/cookie.dat';
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $tmpfname);
		$resp = curl_exec($ch);
		curl_close($ch);
		return $headers;
	}
    
    public function sendDataToCrm($token, $cookie, $arrayLeadData){
		// Encode array data to JSON		
		$jsonData = json_encode($arrayLeadData);
		
		// Initialize CURL
		$ch = curl_init($this->leadCollectionUrl);
		
		// Set CURL options
		curl_setopt($ch, CURLOPT_HTTPHEADER, array (
			'x-csrf-token: ' . $token,
			'Cookie: ' . $cookie,
			'Content-Type: application/json',
			'Content-Length: ' . strlen($jsonData),
			'Accept: application/json'
		));
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

		$result = curl_exec($ch);
		curl_close($ch);
		return [curl_getinfo($ch), $result];
	}	
	
	
	public function addProductsToLeads($token, $cookie, $arrayProductData, $leadsUrl){
		// Encode array data to JSON		
		$jsonData = json_encode($arrayProductData);
		
		// Initialize CURL
		$ch = curl_init(trim($leadsUrl).'/LeadItem');
		
		// Set CURL options
		curl_setopt($ch, CURLOPT_HTTPHEADER, array (
			'x-csrf-token: ' . $token,
			'Cookie: ' . $cookie,
			'Content-Type: application/json',
			'Content-Length: ' . strlen($jsonData),
			'Accept: application/json'
		));
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

		$result = curl_exec($ch);
		return [curl_getinfo($ch), curl_errno($ch), curl_error($ch), $result];
	}
	
	public function getProductCount($token, $cookie){
		// Initialize CURL
		$ch = curl_init($this->productCollectionUrl . '?$top=1&$inlinecount=allpages');
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array (
			'x-csrf-token: ' . $token,
			'Cookie: ' . $cookie,
		));
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);		

		$resp = curl_exec($ch);
		curl_close($ch);
		
		$xResp = str_replace('m:', '', str_replace('d:', '', str_replace('</content>', '</productData>', str_replace('<content type="application/xml">', '<productData>', strstr($resp, '<')))));
		
		$xml   = simplexml_load_string($xResp);
		$array = json_decode(json_encode((array) $xml), true);
		$array = array($xml->getName() => $array);
		
		$countProducts = $array['feed']['count'];
		return [curl_getinfo($ch), curl_error($ch), $xResp, $countProducts];
	}
	
	public function syncProducts($token, $cookie, $page = 0){
		// Initialize local vars
		$count = 0;
		$crmGrabStart = (($page * 1000) + 1);
		
		// Initialize CURL
		$ch = curl_init($this->productCollectionUrl . '?$inlinecount&$skiptoken=' . $crmGrabStart . '%20');
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array (
			'x-csrf-token: ' . $token,
			'Cookie: ' . $cookie,
		));
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);		

		$resp = curl_exec($ch);
		curl_close($ch);
		
		$xResp = str_replace('m:', '', str_replace('d:', '', str_replace('</content>', '</productData>', str_replace('<content type="application/xml">', '<productData>', strstr($resp, '<')))));
		
		$xml   = simplexml_load_string($xResp);
		$array = json_decode(json_encode((array) $xml), true);
		$array = array($xml->getName() => $array);
		
		foreach($array['feed']['entry'] as $arrayProductInfo){
			// Fetch product data
			$arrayProductData = $arrayProductInfo['productData']['properties'];
			if($arrayProductData['ObjectID'] && !is_array($arrayProductData['ExternalID'])){
				$objCrmProductsModel = new CrmProductsModel();
				$objCrmProductsModel->tstamp = time();
				$objCrmProductsModel->object_id = $arrayProductData['ObjectID'];
				$objCrmProductsModel->product_id = $arrayProductData['ProductID'];
				$objCrmProductsModel->uuid = $arrayProductData['UUID'];
				$objCrmProductsModel->base_uom = $arrayProductData['BaseUOM'];
				$objCrmProductsModel->base_uom_text = $arrayProductData['BaseUOMText'];
				$objCrmProductsModel->external_id = intval($arrayProductData['ExternalID']);
				$objCrmProductsModel->save();
				
				$count ++;
			}
		}		
		
		return [curl_getinfo($ch), curl_error($ch), $xResp, $count];
	}
}
