<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Persistence\Write;

use App\Domain\Shared\Aggregate\AggregateRootInterface;
use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Exception\CouldNotFindEventStreamException;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\Message\MessageInterface;
use App\Domain\Shared\Message\MessageSerializer;
use App\Domain\Shared\Message\MessageSerializerInterface;
use App\Domain\Shared\Message\MessageStreamInterface;
use App\Domain\Shared\Message\MessageToAggregateRootSerializerInterface;
use App\Domain\Shared\Persistence\Write\EventStoreInterface;
use App\Infrastructure\Shared\Persistence\Exception\DuplicatePlayheadException;
use App\Infrastructure\Shared\Persistence\Exception\EventStoreException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/**
 * @template T of AggregateRootInterface
 *
 * @template-implements EventStoreInterface<T>
 */
abstract readonly class AbstractDbalEventStore implements EventStoreInterface
{
    /**
     * @phpstan-param MessageSerializerInterface<MessageInterface<EventInterface>>|MessageSerializer $messageSerializer
     */
    protected function __construct(
        protected Connection $connection,
        /** @var MessageToAggregateRootSerializerInterface<T> */
        protected MessageToAggregateRootSerializerInterface $serializer,
        protected string $tableName,
        protected string $modelFQCN,
        protected string $aggregateRootColumn = 'uuid',
        protected MessageSerializerInterface $messageSerializer = new MessageSerializer(),
    ) {
    }

    /**
     * @throws EventStoreException
     * @throws DuplicatePlayheadException
     * @throws Exception
     */
    public function append(
        \Stringable $aggregate,
        MessageStreamInterface $stream,
    ): void {
        $this->connection->beginTransaction();

        try {
            foreach ($stream->getMessages() as $message) {
                $this->connection->insert(
                    table: $this->tableName,
                    data: $this->serializer->serialize(message: $message),
                );
            }

            $this->connection->commit();
        } catch (UniqueConstraintViolationException $exception) {
            $this->connection->rollBack();

            throw new DuplicatePlayheadException(
                messageStream: $stream,
                previous: $exception,
            );
        } catch (\Throwable $exception) {
            $this->connection->rollBack();

            throw new EventStoreException(previous: $exception);
        }
    }

    /**
     * @throws Exception
     * @throws CouldNotFindEventStreamException
     */
    public function load(\Stringable $aggregateRootId): AggregateRootInterface
    {
        return $this->loadFromPlayhead(aggregateRootId: $aggregateRootId);
    }

    /**
     * @throws Exception
     * @throws CouldNotFindEventStreamException
     */
    public function loadFromPlayhead(
        \Stringable $aggregateRootId,
        int $playhead = 0,
    ): AggregateRootInterface {
        $sql = <<<SQL
select * from %s
   where %s = :aggregateRootColumn
     and playhead >= :playhead
order by playhead asc
SQL;
        $sql = sprintf($sql, $this->tableName, $this->aggregateRootColumn);
        $statement = $this->connection->prepare(sql: $sql);
        $statement->bindValue(param: ':aggregateRootColumn', value: (string) $aggregateRootId);
        $statement->bindValue(param: 'playhead', value: $playhead);
        $queryResult = $statement->executeQuery();
        /** @var array<array<string, mixed>> $result */
        $result = $queryResult->fetchAllAssociative();

        if (empty($result)) {
            throw CouldNotFindEventStreamException::forModel(
                modelFQCN: $this->modelFQCN,
                aggregateRootId: (string) $aggregateRootId,
            );
        }

        return $this->serializer->deserialize(data: $result);
    }

    /**
     * @return MessageInterface<EventInterface>[]
     *
     * @throws Exception
     * @throws DateTimeException
     * @throws InvalidAggregateStringProvidedException
     * @throws InvalidUuidStringProvidedException
     */
    public function loadAll(): array
    {
        $sql = <<<SQL
select * from %s
order by playhead asc
SQL;
        $sql = sprintf($sql, $this->tableName);
        $statement = $this->connection->prepare(sql: $sql);
        $queryResult = $statement->executeQuery();
        /** @var array<array<string, mixed>> $result */
        $result = $queryResult->fetchAllAssociative();

        $messages = [];

        foreach ($result as $messageEvent) {
            $messages[] = $this->messageSerializer->deserialize(data: $messageEvent);
        }

        return $messages;
    }
}
