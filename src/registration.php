<?php
/**
 * @author    Raivis Dejus <info@scandiweb.com>
 * @copyright Copyright (c) 2018 Scandiweb, Ltd (https://scandiweb.com)
 */

use \Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'ScandiPWA_Installer',
    __DIR__
);
