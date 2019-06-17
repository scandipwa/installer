<?php
/**
 * ScandiPWA_Installer
 *
 * @category    ScandiPWA
 * @package     ScandiPWA_Installer
 * @author      Ilja Lapkovskis <ilja@scandiweb.com | info@scandiweb.com>
 * @copyright   Copyright (c) 2019 Scandiweb, Ltd (https://scandiweb.com)
 * @license     OSL-3.0
 */

namespace ScandiPWA\Installer\Theme;


use Magento\Framework\Config\Theme;

class Config extends Theme
{
    /**
     * @param string $configContent
     * @return array
     */
    protected function _extractData($configContent)
    {
        $data = parent::_extractData($configContent);
        
        if (!empty($configContent)) {
            $dom = new \DOMDocument();
            $dom->loadXML($configContent);
            /** @var $themeNode \DOMElement */
            $themeNode = $dom->getElementsByTagName('theme')->item(0);
            $themePwaNode = $themeNode->getElementsByTagName('pwa')->item(0);
            $data['pwa'] = $themePwaNode ? $themePwaNode->nodeValue : null;
        }
        
        return $data;
    }
    
    /**
     * @return mixed
     */
    public function isPwa()
    {
        return $this->_data['pwa'];
    }
}
