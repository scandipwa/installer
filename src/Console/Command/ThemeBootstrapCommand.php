<?php
/**
 * @category  ScandiPWA
 * @package   ScandiPWA\Installer
 * @author    Ilja Lapkovskis <info@scandiweb.com / ilja@scandiweb.com>
 * @copyright Copyright (c) 2019 Scandiweb, Ltd (http://scandiweb.com)
 * @license   OSL-3.0
 */


namespace ScandiPWA\Installer\Console\Command;

use Magento\Framework\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\QuestionFactory;


/**
 * Class ThemeBootstrap
 *
 * @package ScandiPWA\Installer\Console\Command
 */
class ThemeBootstrapCommand extends Command
{
    const THEME_DIR = 'design/frontend';
    
    const SECTION = 'frontend';
    
    const SOURCE_THEME_NAME = 'ScandiPWA_Source';
    
    /**
     * @var Bootstrap
     */
    private $bootstrap;
    
    /**
     * @var QuestionFactory
     */
    private $question;
    
    /**
     * ThemeBootstrap constructor.
     *
     * @param Filesystem $fs
     * @param null       $name
     */
    public function __construct(Bootstrap $bootstrap, QuestionFactory $question, $name = null)
    {
        parent::__construct($name);
        $this->bootstrap = $bootstrap;
        $this->question = $question;
    }
    
    /**
     * Define Symfony\Console compatible command
     */
    protected function configure()
    {
        $this->setName('scandipwa:theme:bootstrap')
            ->setDescription('Bootstraps ScandiPWA theme')
            ->addArgument('name', InputArgument::REQUIRED, 'Put the theme name you want to create');
        
        parent::configure();
    }
    
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepareOutput($output);
        $themeName = $input->getArgument('name');
        
        // On success return 0, as these are EXIT codes
        $copyCode = $this->bootstrap->copy($themeName, $output);
        if ($copyCode) {
            return $copyCode;
        }
        $noInteraction = $input->getOption('no-interaction');
        if (!$noInteraction) {
            $question = $this->question->create([
                'question' => 'Do you want to automatically generate registration.php? [y/N]',
                'default' => false,
            ]);
            $questionHelper = new QuestionHelper();
            $generateRegistarion = $questionHelper->ask($input, $output, $question);
            if (!$generateRegistarion) {
                $output->writeln('<success> You\'re done! </success>');
                
                return 0;
            }
        }
        
        
        return $this->generateThemeFiles($themeName, $output);
    }
    
    /**
     * @param string          $themeName
     * @param OutputInterface $output
     * @return int
     */
    protected function generateThemeFiles(string $themeName, OutputInterface $output)
    {
        $registration = $this->bootstrap->generateRegistration($themeName);
        $themeXml = $this->bootstrap->generateThemeXml($themeName);
        if ($registration < 1 || $themeXml < 1) {
            $output->writeln('<error>Failed to generate files</error>');
            return 9;
        }
        
        $output->writeln('<success>ScandiPWA new theme bootstrap done! Happy coding!</success>');
        $output->writeln(sprintf('<warn>Now you can build the theme</warn>: cd app/design/frontend/%s && npm ci && npm run build',
            $themeName));
        $output->writeln('<info>Read the docs: https://docs.scandipwa.com</info>');
        
        return 0;
    }
    
    /**
     * @param OutputInterface $output
     * @return OutputInterface
     */
    protected function prepareOutput(OutputInterface $output)
    {
        $error = new OutputFormatterStyle('red', 'black', ['bold', 'blink']);
        $warn = new OutputFormatterStyle('yellow', 'black', ['bold', 'blink']);
        $success = new OutputFormatterStyle('green', 'black', ['bold', 'blink']);
        $special = new OutputFormatterStyle('blue', 'black', ['bold', 'blink']);
        $output->getFormatter()->setStyle('error', $error);
        $output->getFormatter()->setStyle('warn', $warn);
        $output->getFormatter()->setStyle('success', $success);
        $output->getFormatter()->setStyle('special', $special);
        
        return $output;
    }
}
