<?php
declare(strict_types=1);

namespace Eurotext\TranslationManager\Console\Command;

use Eurotext\TranslationManager\Console\Service\AddEntityService;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddEntityCommand extends Command
{
    const NAME        = 'etm:entity:add';
    const DESCRIPTION = 'Adds one or more entites to an existing project';

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var AddEntityService
     */
    private $addEntityService;

    public function __construct(
        AddEntityService $addEntityService,
        AppState $appState
    ) {
        parent::__construct();
        $this->appState         = $appState;
        $this->addEntityService = $addEntityService;
    }

    protected function configure()
    {
        $this->setName(self::NAME);
        $this->setDescription(self::DESCRIPTION);

        $this->addArgument(
            AddEntityService::ARG_PROJECT_ID, InputArgument::REQUIRED, AddEntityService::ARG_PROJECT_ID_DESC
        );
        $this->addArgument(
            AddEntityService::ARG_ENTITY_TYPE, InputArgument::REQUIRED, AddEntityService::ARG_ENTITY_TYPE_DESC
        );
        $this->addArgument(
            AddEntityService::ARG_ENTITY_ID, InputArgument::REQUIRED, AddEntityService::ARG_ENTITY_ID_DESC
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

        $this->addEntityService->execute($input, $output);
    }
} 
