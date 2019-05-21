<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Logger;

use Monolog\Handler\HandlerInterface;
use Monolog\Handler\PsrHandler;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class PushConsoleLogHandler
{
    /**
     * @var ApiLogger
     */
    private $apiLogger;

    public function __construct(ApiLogger $logger)
    {
        $this->apiLogger = $logger;
    }

    public function push(OutputInterface $output)
    {
        $consoleLogHandler = $this->createConsoleLogHandler($output);

        // Push the ConsoleLogger to the EurotextLogger so we directly see console output
        $this->apiLogger->pushHandler($consoleLogHandler);
    }

    private function createConsoleLogHandler(OutputInterface $output): HandlerInterface
    {
        $consoleLogger = new ConsoleLogger(
            $output,
            [
                LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
                LogLevel::INFO   => OutputInterface::VERBOSITY_NORMAL,
            ]
        );

        return new PsrHandler($consoleLogger);
    }
}