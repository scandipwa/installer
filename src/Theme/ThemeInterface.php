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


use Magento\Framework\View\Design\ThemeInterface as MagentoThemeInterface;

interface ThemeInterface extends MagentoThemeInterface
{
    /**
     * PWA theme type
     */
    const TYPE_PWA = 4;
}
