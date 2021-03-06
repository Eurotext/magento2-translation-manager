<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Console\Command;

use Eurotext\TranslationManager\Console\Service\SeedEntitiesService;
use Eurotext\TranslationManager\Logger\PushConsoleLogHandler;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedEntitiesCommand extends Command
{
    const NAME        = 'etm:entity:seed';
    const DESCRIPTION = 'Seed entites for a given project';

    /**
     * @var SeedEntitiesService
     */
    private $seedEntitiesService;

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var PushConsoleLogHandler
     */
    private $pushConsoleLog;

    public function __construct(
        SeedEntitiesService $seedEntitiesService,
        PushConsoleLogHandler $pushConsoleLog,
        AppState $appState
    ) {
        parent::__construct();
        $this->seedEntitiesService = $seedEntitiesService;
        $this->appState            = $appState;
        $this->pushConsoleLog      = $pushConsoleLog;
    }

    protected function configure()
    {
        $this->setName(self::NAME);
        $this->setDescription(self::DESCRIPTION);

        $this->addArgument(
            SeedEntitiesService::ARG_PROJECT_ID, InputArgument::REQUIRED, SeedEntitiesService::ARG_PROJECT_ID_DESC
        );
        $this->addArgument(
            SeedEntitiesService::ARG_ENTITIES, InputArgument::REQUIRED, SeedEntitiesService::ARG_ENTITY_DESC
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode('adminhtml');
        } catch (LocalizedException $e) {
            // the area code is already set
        }

        $this->pushConsoleLog->push($output);

        $this->seedEntitiesService->execute($input, $output);
    }
} 
