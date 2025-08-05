<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Command;

use App\Domain\Shared\Event\EventCriteria;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Infrastructure\TodoList\Replay\ReplayTodoListAggregate;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'app:replay:todo-list',
    description: 'Replay the Todo List events',
    hidden: false,
)]
final class ReplayTodoListEventsCommand extends Command
{
    private const array SUGGESTED_VALUES = [
        '1',
        '0',
        true,
        false,
        1,
        0,
    ];

    public function __construct(
        private readonly ReplayTodoListAggregate $replayTodoListAggregate,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                name: 'forAggregateId',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Replay the events for a specific aggregate id',
            )
            ->addOption(
                name: 'hasMultipleAggregateIds',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Replay the events for multiple aggregate ids',
                suggestedValues: self::SUGGESTED_VALUES
            )
            ->addOption(
                name: 'ignoreAggregateId',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Replay the events for all aggregates except for a specific aggregate id',
            )
            ->addOption(
                name: 'ignoreMultipleAggregateIds',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Replay the events for all aggregates except for multiple aggregate ids',
            )
            ->addOption(
                name: 'forEventType',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Replay the events for a specific event type',
            )
            ->addOption(
                name: 'hasMultipleEventTypes',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Replay the events for multiple event types',
                suggestedValues: self::SUGGESTED_VALUES
            )
            ->addOption(
                name: 'ignoreEventType',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Replay the events for all event types except for a specific event type',
            )
            ->addOption(
                name: 'ignoreMultipleEventTypes',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Replay the events for all event types except for multiple event types',
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $forAggregateIds = [];
        $forAggregateIdsToIgnore = [];
        $forEventTypes = [];
        $forEventTypesToIgnore = [];

        $helper = new QuestionHelper();

        if (null !== $input->getOption('forAggregateId')) {
            $aggregate = $this->askForSingleAggregate(
                input: $input,
                output: $output,
                helper: $helper,
            );

            if ($aggregate instanceof AggregateRootId) {
                $forAggregateIds[] = $aggregate;
            }
        }

        if (null !== $input->getOption('hasMultipleAggregateIds')) {
            $forAggregateIds = array_merge(
                $forAggregateIds,
                $this->askForMultipleAggregates(
                    input: $input,
                    output: $output,
                    helper: $helper,
                )
            );
        }

        if (null !== $input->getOption('ignoreAggregateId')) {
            $aggregate = $this->askForSingleAggregate(
                input: $input,
                output: $output,
                helper: $helper,
                toIgnore: true,
            );

            if ($aggregate instanceof AggregateRootId) {
                $forAggregateIdsToIgnore[] = $aggregate;
            }
        }

        if (null !== $input->getOption('ignoreMultipleAggregateIds')) {
            $forAggregateIdsToIgnore = array_merge(
                $forAggregateIdsToIgnore,
                $this->askForMultipleAggregates(
                    input: $input,
                    output: $output,
                    helper: $helper,
                    toIgnore: true,
                )
            );
        }

        if (null !== $input->getOption('forEventType')) {
            /** @var string $forEventType */
            $forEventType = $input->getOption('forEventType');
            $forEventTypes[] = $forEventType;
        }

        if (null !== $input->getOption('ignoreEventType')) {
            /** @var string $eventTypeToIgnore */
            $eventTypeToIgnore = $input->getOption('ignoreEventType');

            $forEventTypesToIgnore[] = $eventTypeToIgnore;
        }

        if (null !== $input->getOption('hasMultipleEventTypes')) {
            do {
                $question = new Question(question: 'For which event type do you want to check? [Leave blank to stop]');

                /** @var string|null $forEventType */
                $forEventType = $helper->ask($input, $output, $question);

                if (is_string($forEventType)) {
                    $forEventTypes[] = $forEventType;
                }
            } while (!empty($forEventType));
        }

        if (null !== $input->getOption('ignoreMultipleEventTypes')) {
            do {
                $question = new Question(question: 'Which event type do you want to ignore? [Leave blank to stop]');

                $eventTypeToIgnore = $helper->ask($input, $output, $question);

                if (is_string($eventTypeToIgnore)) {
                    $forEventTypesToIgnore[] = $eventTypeToIgnore;
                }
            } while (!empty($eventTypeToIgnore));
        }

        $criteria = EventCriteria::create();

        $this->replayTodoListAggregate
            ->withCriteria(
                criteria: $criteria
                    ->withAggregateRootId(...$forAggregateIds)
                    ->withAggregateRootIdToIgnore(...$forAggregateIdsToIgnore)
                    ->withEventTypes(...$forEventTypes)
                    ->withEventTypesToIgnore(...$forEventTypesToIgnore)
            )
            ->replay()
        ;

        return self::SUCCESS;
    }

    /**
     * @phpstan-return AggregateRootId[]
     */
    private function askForMultipleAggregates(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $helper,
        bool $toIgnore = false,
    ): array {
        $aggregates = [];
        do {
            $aggregate = $this->getAggregateIdFromInput(
                input: $input,
                output: $output,
                helper: $helper,
                toIgnore: $toIgnore,
            );

            if ($aggregate instanceof AggregateRootId) {
                $aggregates[] = $aggregate;
            }
        } while ($aggregate instanceof AggregateRootId);

        return $aggregates;
    }

    private function askForSingleAggregate(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $helper,
        bool $toIgnore = false,
    ): ?AggregateRootId {
        $providedAggregateId = null;

        if ($input->hasOption('forAggregateId')) {
            /** @var non-empty-string $providedAggregateId */
            $providedAggregateId = $input->getOption('forAggregateId');
        }

        if ($input->hasOption('ignoreAggregateId')) {
            /** @var non-empty-string $providedAggregateId */
            $providedAggregateId = $input->getOption('ignoreAggregateId');
        }

        return $this->getAggregateIdFromInput(
            input: $input,
            output: $output,
            helper: $helper,
            providedInput: $providedAggregateId,
            toIgnore: $toIgnore,
        );
    }

    /**
     * @phpstan-param non-empty-string|null $providedInput
     */
    private function getAggregateIdFromInput(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $helper,
        ?string $providedInput = null,
        bool $toIgnore = false,
    ): ?AggregateRootId {
        $wantToContinueQuestion = new ConfirmationQuestion(question: 'Do you want to continue?', default: false);
        $aggregateCheckQuestion = $toIgnore ? 'For which AggregateId do you want to check? [Leave blank to stop]' : 'Which AggregateId do you want to ignore? [Leave blank to stop]';

        if (is_string($providedInput)) {
            $aggregate = $this->checkAggregateId(
                aggregateId: $providedInput,
                input: $input,
                output: $output,
                helper: $helper,
                wantToContinueQuestion: $wantToContinueQuestion,
            );

            if ($aggregate instanceof AggregateRootId) {
                return $aggregate;
            }

            if (false === $aggregate) {
                return null;
            }
        }

        do {
            $aggregateRootIdToCheckQuestion = new Question(question: $aggregateCheckQuestion);
            $aggregateRootIdToCheckQuestion->setTrimmable(trimmable: false);

            /** @var non-empty-string $aggregateRootIdToCheckAnswer */
            $aggregateRootIdToCheckAnswer = $helper->ask($input, $output, $aggregateRootIdToCheckQuestion);

            if (empty($aggregateRootIdToCheckAnswer)) {
                return null;
            }

            $aggregate = $this->checkAggregateId(
                aggregateId: $aggregateRootIdToCheckAnswer,
                input: $input,
                output: $output,
                helper: $helper,
                wantToContinueQuestion: $wantToContinueQuestion,
            );

            if ($aggregate instanceof AggregateRootId) {
                return $aggregate;
            }

            $askForAggregateId = $aggregate;
        } while ($askForAggregateId);

        return null;
    }

    /**
     * @param non-empty-string $aggregateId
     */
    private function checkAggregateId(
        string $aggregateId,
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $helper,
        ConfirmationQuestion $wantToContinueQuestion,
    ): AggregateRootId|bool {
        try {
            return AggregateRootId::fromString(value: $aggregateId);
        } catch (ValueObjectDidNotMeetValidationException $e) {
            $output->writeln(messages: sprintf('<error>%s</error>', $e->getMessage()));
        }

        if (!$helper->ask($input, $output, $wantToContinueQuestion)) {
            return false;
        }

        return true;
    }
}
