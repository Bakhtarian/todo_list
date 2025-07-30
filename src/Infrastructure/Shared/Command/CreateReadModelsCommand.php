<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Command;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:read:create',
    description: 'Create read models',
    hidden: false,
)]
final class CreateReadModelsCommand extends Command
{
    private const array COLLECTIONS = [
        'overview',
        'detail-view',
        'notified',
    ];

    public function __construct(
        private readonly DocumentManager $documentManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            foreach (self::COLLECTIONS as $collection) {
                $this->documentManager
                    ->getClient()
                    ->getDatabase('read_models')
                    ->createCollection($collection);

                $output->writeln(sprintf('<info>Create collection for %s</info>', $collection));
            }
        } catch (\Throwable $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return self::FAILURE;
        }

        $output->writeln('<info>Read models created</info>');

        return self::SUCCESS;
    }
}
