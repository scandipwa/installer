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


use \Magento\Theme\Model\Theme\Data\Collection;

class ThemeList extends Collection
{
    /**
     * @param \Magento\Framework\View\Design\Theme\ThemePackage $themePackage
     * @return array
     */
    protected function _prepareConfigurationData($themePackage)
    {
        $theme = parent::_prepareConfigurationData($themePackage);
        /**
         * @var ScandiPWA\Installer\Theme\Config $themeConfig
         */
        $themeConfig = $this->_getConfigModel($themePackage);
        
        
        if ($themeConfig->isPwa()) {
            $theme['type'] = ThemeInterface::TYPE_PWA;
        }
        
        return $theme;
    }
}
