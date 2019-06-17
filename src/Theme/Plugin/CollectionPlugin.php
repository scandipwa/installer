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

namespace ScandiPWA\Installer\Theme\Plugin;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class CollectionPlugin
{
    /**
     * @param AbstractCollection $subject
     * @param                    $next
     * @return AbstractCollection
     */
    public function aroundFilterVisibleThemes(AbstractCollection $subject, callable $next)
    {
        $subject->addTypeFilter(
            [
                \Magento\Framework\View\Design\ThemeInterface::TYPE_PHYSICAL,
                \Magento\Framework\View\Design\ThemeInterface::TYPE_VIRTUAL,
                \ScandiPWA\Installer\Theme\ThemeInterface::TYPE_PWA
            ]
        );
        
        return $subject;
    }
}