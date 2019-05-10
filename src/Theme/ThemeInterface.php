<?php


namespace ScandiPWA\Installer\Theme;


use Magento\Framework\View\Design\ThemeInterface as MagentoThemeInterface;

interface ThemeInterface extends MagentoThemeInterface
{
    /**
     * PWA theme type
     */
    const TYPE_PWA = 4;
}
