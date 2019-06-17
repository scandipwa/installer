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


use Magento\Framework\View\Design\Theme\Validator as MagentoValidator;

class Validator extends MagentoValidator
{
    protected function _setTypeValidators()
    {
        $typeValidators = [
            [
                'name' => 'not_empty',
                'class' => 'Zend_Validate_NotEmpty',
                'break' => true,
                'options' => [],
                'message' => (string)new \Magento\Framework\Phrase('Theme type can\'t be empty'),
            ],
            [
                'name' => 'available',
                'class' => 'Zend_Validate_InArray',
                'break' => true,
                'options' => [
                    'haystack' => [
                        \Magento\Framework\View\Design\ThemeInterface::TYPE_PHYSICAL,
                        \Magento\Framework\View\Design\ThemeInterface::TYPE_VIRTUAL,
                        \Magento\Framework\View\Design\ThemeInterface::TYPE_STAGING,
                        ThemeInterface::TYPE_PWA
                    ],
                ],
                'message' => (string)new \Magento\Framework\Phrase('Theme type is invalid')
            ],
        ];
        
        $this->addDataValidators('type', $typeValidators);
        
        return $this;
    }
}
