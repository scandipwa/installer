<?php
/**
 * @category  ScandiPWA
 * @package   ScandiPWA\Installer
 * @author    Ilja Lapkovskis <info@scandiweb.com / ilja@scandiweb.com>
 * @copyright Copyright (c) 2019 Scandiweb, Ltd (http://scandiweb.com)
 * @license   Apache-2.0
 */

namespace ScandiPWA\Installer\Console\Command;


use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Copy
 *
 * @package ScandiPWA\Installer\Console\Command
 */
class Bootstrap
{
    /**
     * @var Filesystem\Directory\ReadInterface
     */
    private $appRead;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $appWrite;

    /**
     * @var Filesystem\Directory\ReadInterface
     */
    private $baseReader;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $baseWriter;

    /**
     * @var array
     */
    private $copyQueue;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var string|null
     */
    private $sourcePath;

    /**
     * @var string
     */
    private $themeName;

    const THEME_REGISTRATION_TEMPLATE = <<<EOT
<?php
/**
 * @autogenerated
 * @copyright Copyright (c) 2019 Scandiweb, Ltd (https://scandiweb.com)
 */

use \Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::THEME,
    '{{THEME_NAME}}',
    __DIR__
);

EOT;

    const THEME_XML = <<<EOT
<?xml version="1.0"?>
<!--
/**
 * @autogenerated
 * @copyright Copyright (c) 2019 Scandiweb, Ltd (https://scandiweb.com)
 */
-->
<theme xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Config/etc/theme.xsd">
    <title>{{THEME_NAME}}</title>
    <parent>Magento/blank</parent>
</theme>

EOT;



    /**
     * Copy constructor.
     *
     * @param Filesystem $fs
     * @param ComponentRegistrarInterface $registrar
     * @param array $copyQueue
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Filesystem $fs,
        ComponentRegistrarInterface $registrar,
        array $copyQueue = []
    ) {
        $this->copyQueue = $copyQueue;
        $this->sourcePath = $registrar->getPath(ComponentRegistrar::MODULE, ThemeBootstrapCommand::SOURCE_THEME_NAME);
        $this->appRead = $fs->getDirectoryRead(DirectoryList::APP);
        $this->appWrite = $fs->getDirectoryWrite(DirectoryList::APP);
        $this->baseReader = $fs->getDirectoryRead(DirectoryList::ROOT);
        $this->baseWriter = $fs->getDirectoryWrite(DirectoryList::ROOT);
    }

    /**
     * @param string $themeName
     * @param OutputInterface $output
     * @return int|mixed
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function copy(string $themeName, OutputInterface $output)
    {
        $this->output = $output;
        $this->themeName = $themeName;

        $output->write('Checking prerequisite...');
        try {
            $this->validate($themeName);
        } catch (ScandiPWABootstrapException $e) {
            $output->writeln('<error> Failed</error>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return $e->getCode();
        }

        $output->writeln('<success> Done</success>');
        $output->writeln('<success>Theme not set up. Setting up...</success>');
        $output->writeln('<success>Copying files...</success>');

        if ($this->copyFiles($this->copyQueue)) {
            return 0;
        }

        return 1;
    }

    public function generateRegistration(string $themeName)
    {
        $destinationPath = $this->getThemePath($themeName);
        return $this->appWrite->writeFile(
            $destinationPath . DIRECTORY_SEPARATOR . 'registration.php',
            str_replace(
                '{{THEME_NAME}}',
                ThemeBootstrapCommand::SECTION . DIRECTORY_SEPARATOR . $themeName,
                self::THEME_REGISTRATION_TEMPLATE)
        );
    }

    public function generateThemeXml(string $themeName)
    {
        $destinationPath = $this->getThemePath($themeName);
        return $this->appWrite->writeFile(
            $destinationPath . DIRECTORY_SEPARATOR . 'theme.xml',
            str_replace(
                '{{THEME_NAME}}',
                str_replace('/', ' ', $themeName . ' theme'),
                self::THEME_XML
            )
        );
    }

    protected function getThemePath($themeName)
    {
        return $destinationPath = $this->appRead->getAbsolutePath(
            ThemeBootstrapCommand::THEME_DIR . DIRECTORY_SEPARATOR . $themeName);
    }

    /**
     * @param string $path
     * @param string $sourceFilePath
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function copyDirectory(string $path, string $sourceFilePath): bool
    {
        if (substr($path, -1) !== '/') {
            $path .= '/';
        }
        $directoryQueue = $this->getSubDirFiles($sourceFilePath);
        $subDirQueue = array_map(function ($subDirItem) use ($path) {
            return $path . $subDirItem;
        }, $directoryQueue);

        return $this->copyFiles($subDirQueue);
    }
    
    /**
     * @param array $copyQueue
     * @return bool
     * @throws ScandiPWABootstrapException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function copyFiles(array $copyQueue): bool
    {
        if (count($copyQueue) < 1) {
            return false;
        }
    
        $output = $this->output;
        $sourceFolder = $this->baseReader->getRelativePath($this->sourcePath) . DIRECTORY_SEPARATOR;
        $destinationFolder = $this->appRead->getAbsolutePath(
            ThemeBootstrapCommand::THEME_DIR . DIRECTORY_SEPARATOR . $this->themeName);
        $destinationFolder = $this->baseWriter->getRelativePath($destinationFolder) . DIRECTORY_SEPARATOR;
        
        foreach ($copyQueue as $key => $item) {
            if (is_array($item)) {
                $sourceFilePath = $sourceFolder . $item['source'];
                $destinationFilePath = $destinationFolder . $item['destination'];
            } else {
                $sourceFilePath = $sourceFolder . $item;
                $destinationFilePath = $destinationFolder . $item;
            }

            if ($this->baseReader->isDirectory($sourceFilePath)) {
                unset($copyQueue[$key]);
                $output->writeln(sprintf('Copying DIR: <special>%s</special>', $destinationFilePath));
                if (is_array($item)) {
                    throw new ScandiPWABootstrapException('Directory definition can not be array');
                }
                if ($this->copyDirectory($item, $sourceFilePath)) {
                    $output->writeln(sprintf('Finished DIR: <special>%s</special>', $destinationFilePath));
                    continue;
                }
                $output->writeln('Error copying dir: ' . $destinationFilePath);
            }

            $output->write('Copying <special>' . $destinationFilePath . '</special>');
            $copyingResult = $this->baseWriter->copyFile(
                $sourceFilePath,
                $destinationFilePath
            );

            $output->writeln($copyingResult ? '<success> Done</success>' : '<error> Failed</error>');
        }

        return true;
    }

    /**
     * @param string $directory
     * @return array
     */
    protected function getSubDirFiles($directory): array
    {
        $dirList = scandir($this->baseReader->getAbsolutePath($directory), 0);
        if (!count($dirList)) {
            return [];
        }
        $dirFiles = array_filter($dirList, function ($item) {
            return ($item !== '.' && $item !== '..');
        });

        return $dirFiles;
    }

    /**
     * @param $themeName
     * @return bool
     * @throws ScandiPWABootstrapException
     */
    protected function isDestDirPresent($themeName): bool
    {
        if ($this->appRead->isDirectory(ThemeBootstrapCommand::THEME_DIR . DIRECTORY_SEPARATOR . $themeName)) {
            throw new ScandiPWABootstrapException(
                'Theme already present. Please choose another name or remove manually',
                97);
        }

        return true;
    }

    /**
     * @return bool
     * @throws ScandiPWABootstrapException
     */
    protected function isSourcePathAvailable(): bool
    {
        if ($this->sourcePath === null) {
            throw new ScandiPWABootstrapException(
                'Sources are missing, have you installed the source package?',
                98);
        }

        return true;
    }

    /**
     * @param $themeName
     * @return bool
     * @throws ScandiPWABootstrapException
     */
    protected function validate($themeName): bool
    {
        return $this->isSourcePathAvailable() && $this->isDestDirPresent($themeName);
    }
}
