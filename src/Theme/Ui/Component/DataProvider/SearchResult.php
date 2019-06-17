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

namespace ScandiPWA\Installer\Theme\Ui\Component\DataProvider;

use ScandiPWA\Installer\Theme\ThemeInterface;

class SearchResult extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * {@inheritdoc}
     */
    protected $_map = [
        'fields' => [
            'theme_id' => 'main_table.theme_id',
            'theme_title' => 'main_table.theme_title',
            'theme_path' => 'main_table.theme_path',
            'parent_theme_title' => 'parent.theme_title',
        ],
    ];
    
    
    /**
     * Add area and type filters
     * Join parent theme title
     *
     * @return \Magento\Theme\Ui\Component\Theme\DataProvider\SearchResult
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this
            ->addFieldToFilter('main_table.area', \Magento\Framework\App\Area::AREA_FRONTEND)
            ->addFieldToFilter('main_table.type', ['in' => [
                \Magento\Framework\View\Design\ThemeInterface::TYPE_PHYSICAL,
                \Magento\Framework\View\Design\ThemeInterface::TYPE_VIRTUAL,
                ThemeInterface::TYPE_PWA
            ]]);
        
        $this->getSelect()->joinLeft(
            ['parent' => $this->getMainTable()],
            'main_table.parent_id = parent.theme_id',
            ['parent_theme_title' => 'parent.theme_title']
        );
        
        return $this;
    }
}