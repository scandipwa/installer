<?php
/**
 * ScandiPWA_Installer
 *
 * @category    ScandiPWA
 * @package     ScandiPWA_Installer
 * @author      Alfreds Genkins <info@scandiweb.com>
 * @copyright   Copyright (c) 2020 Scandiweb, Ltd (https://scandiweb.com)
 * @license     OSL-3.0
 */

namespace ScandiPWA\Installer\Theme\Plugin;

use Magento\Theme\Model\Theme;
use ScandiPWA\Installer\Theme\ThemeInterface;

class ThemePlugin {
    public function afterIsVisible(Theme $subject, $result) {
        return $result ? $result : (int) $subject->getType() === ThemeInterface::TYPE_PWA;
    }
}
