<?php

if (PHP_SAPI == 'cli') {
    \Magento\Framework\Console\CommandLocator::register(\ScandiPWA\Installer\Console\CommandList::class);
}
