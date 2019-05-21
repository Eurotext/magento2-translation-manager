<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Console\Command;

use Eurotext\TranslationManager\Entity\EntityTypePool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListEntitiesCommand extends Command
{
    const COMMAND_NAME        = 'etm:entity:types';
    const COMMAND_DESCRIPTION = 'List all Entities available';

    /**
     * @var EntityTypePool
     */
    private $entityPool;

    public function __construct(EntityTypePool $entityPool)
    {
        parent::__construct();

        $this->entityPool = $entityPool;
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entities = $this->entityPool->getItems();

        $table = new \Symfony\Component\Console\Helper\Table($output);
        $table->setHeaders(['code', 'description']);

        foreach ($entities as $typeKey => $entityType) {
            $code = $entityType->getCode();
            $desc = $entityType->getDescription();

            $table->addRow([$code, $desc]);
        }
        $table->render();
    }
} 
