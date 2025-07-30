<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:store:create',
    description: 'Create event store',
    hidden: false,
)]
final class CreateEventStoreCommand extends Command
{
    public function __construct(
        private readonly Connection $connection,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sql = <<<SQL
create table if not exists todo_list_events (
         uuid uuid not null
    ,playhead bigint not null
     ,payload jsonb not null
   ,meta_data jsonb default null
 ,recorded_at timestamp(0) not null
        ,type varchar(255) not null
 ,primary key (uuid, playhead)
)
SQL;
        try {
            $this->connection->executeQuery(sql: $sql);
        } catch (\Throwable $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return self::FAILURE;
        }

        $output->writeln('<info>Event store created</info>');

        return self::SUCCESS;
    }
}
