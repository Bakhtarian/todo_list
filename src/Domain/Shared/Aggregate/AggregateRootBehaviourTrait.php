<?php

namespace App\Domain\Shared\Aggregate;

use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Exception\BusinessRuleValidationException;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\Message\Message;
use App\Domain\Shared\Message\MessageInterface;
use App\Domain\Shared\Message\MessageStream;
use App\Domain\Shared\Message\MessageStreamInterface;
use App\Domain\Shared\Specification\Rule\BusinessRuleSpecificationInterface;

trait AggregateRootBehaviourTrait
{
    abstract public function getAggregateRootId(): \Stringable;

    /**
     * @var MessageInterface<EventInterface>[]
     */
    private array $uncommittedEvents = [];

    private int $playhead = -1;

    /**
     * @throws MissingMethodToApplyEventException
     * @throws DateTimeException
     * @throws ValueObjectDidNotMeetValidationException
     */
    public function apply(
        EventInterface $event,
        array $withMetaData = [],
    ): void {
        $this->handle(event: $event);

        ++$this->playhead;
        $this->uncommittedEvents[] = Message::recordNow(
            aggregateId: $this->getAggregateRootId(),
            playhead: $this->playhead,
            metaData: $withMetaData,
            payload: $event,
        );
    }

    /**
     * @throws MissingMethodToApplyEventException
     */
    private function handle(EventInterface $event): void
    {
        $method = $this->getApplyMethod(event: $event);

        if (!method_exists($this, $method)) {
            throw MissingMethodToApplyEventException::forEvent(event: $event::class);
        }

        $this->{$method}(event: $event);
    }

    private function getApplyMethod(EventInterface $event): string
    {
        $classParts = explode('\\', $event::class);

        return sprintf('apply%s', end(array: $classParts));
    }

    /**
     * @template T of MessageInterface<EventInterface>
     *
     * @param MessageStreamInterface<T> $stream
     *
     * @throws InvalidAggregateStringProvidedException
     * @throws MissingMethodToApplyEventException
     * @throws InvalidUuidStringProvidedException
     */
    public function reconstructFromStream(MessageStreamInterface $stream): void
    {
        foreach ($stream->getMessages() as $message) {
            ++$this->playhead;
            $this->handle(event: $message->getPayload());
        }
    }

    /**
     * @return MessageStreamInterface<MessageInterface<EventInterface>>
     */
    public function getUncommittedMessages(): MessageStreamInterface
    {
        return new MessageStream(messages: $this->uncommittedEvents);
    }

    /**
     * @throws BusinessRuleValidationException
     */
    protected static function checkRule(BusinessRuleSpecificationInterface $businessRuleSpecification): void
    {
        if ($businessRuleSpecification->isSatisfiedBy()->isTrue()) {
            return;
        }

        throw new BusinessRuleValidationException(businessRuleSpecification: $businessRuleSpecification);
    }
}
