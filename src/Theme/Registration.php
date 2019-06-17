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


use Magento\Framework\Exception\LocalizedException;
use Magento\Theme\Model\Theme\Registration as MagentoRegistration;
use ScandiPWA\Installer\Theme\ThemeInterface;


class Registration extends MagentoRegistration
{
    /**
     * Register theme and recursively all its ascendants
     * Second param is optional and is used to prevent circular references in inheritance chain
     *
     * @param ThemeInterface &$theme
     * @param array           $inheritanceChain
     * @return \Magento\Theme\Model\Theme\Registration
     * @throws LocalizedException
     */
    protected function _registerThemeRecursively(&$theme, $inheritanceChain = [])
    {
        if ($theme->getId()) {
            return $this;
        }
        $themeModel = $this->getThemeFromDb($theme->getFullPath());
        if ($themeModel->getId()) {
            $theme = $themeModel;
            return $this;
        }
        
        $tempId = $theme->getFullPath();
        if (in_array($tempId, $inheritanceChain)) {
            throw new LocalizedException(__('Circular-reference in theme inheritance detected for "%1"', $tempId));
        }
        $inheritanceChain[] = $tempId;
        $parentTheme = $theme->getParentTheme();
        if ($parentTheme) {
            $this->_registerThemeRecursively($parentTheme, $inheritanceChain);
            $theme->setParentId($parentTheme->getId());
        }
        
        $this->_savePreviewImage($theme);
        $theme->setType($theme->getType() === ThemeInterface::TYPE_PWA ?
            ThemeInterface::TYPE_PWA :
            ThemeInterface::TYPE_PHYSICAL);
        $theme->save();
        
        return $this;
    }
}
