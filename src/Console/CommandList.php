<?php


namespace ScandiPWA\Installer\Console;


use Magento\Framework\Console\CommandListInterface;
use Magento\Framework\ObjectManagerInterface;
use ScandiPWA\Installer\Console\Command\ThemeBootstrapCommand;

class CommandList implements CommandListInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    
    /**
     * CommandList constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }
    
    protected function getCommandClasses(): array
    {
        return [
            ThemeBootstrapCommand::class,
        ];
    }
    
    /**
     * @return array|\Symfony\Component\Console\Command\Command[]
     * @throws \Exception
     */
    public function getCommands()
    {
        $commands = [];
        foreach ($this->getCommandClasses() as $class) {
            if (!\class_exists($class)) {
                throw new \Exception(sprintf('Class %s does not exist', $class));
            }
            $commands[] = $this->objectManager->get($class);
        }
        
        return $commands;
    }
}