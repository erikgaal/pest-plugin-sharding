<?php

declare(strict_types=1);

namespace Pest\Sharding;

use InvalidArgumentException;
use Pest\Contracts\Plugins\HandlesArguments;
use Pest\Plugins\Concerns\HandleArguments;
use Pest\Sharding\TestFinder\TestFinder;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @internal
 */
final class Plugin implements HandlesArguments
{
    use HandleArguments;

    const OPTIONS = ['--shard', '--shard-index', '--shard-total'];

    public function __construct(
        private readonly InputInterface $input,
        private readonly TestFinder $testFinder,
    ) {
    }

    public function handleArguments(array $arguments): array
    {
        if (($shardingArgs = $this->shardingArgs()) === false) {
            return $arguments;
        }

        [$index, $total] = $shardingArgs;

        $arguments = $this->removeShardingArgs($arguments);

        $tests = $this->testFinder->allTests($arguments);
        $testsToRun = self::arraySplit($tests, $total)[$index - 1] ?? [];

        return [...$arguments, '--filter', $this->buildFilterArgument($testsToRun)];
    }

    /**
     * @return array{positive-int, positive-int} | false
     */
    private function shardingArgs(): array|false
    {
        if ($this->input->hasParameterOption('--shard')) {
            $shard = $this->input->getParameterOption('--shard');

            if (! is_string($shard)) {
                throw new InvalidArgumentException('Invalid sharding format. Use --shard=1/8');
            }

            $shard = explode('/', $shard);

            if (count($shard) !== 2) {
                throw new InvalidArgumentException('Invalid sharding format. Use --shard=1/8');
            }

            [$index, $total] = [(int) $shard[0], (int) $shard[1]];
        } elseif ($this->input->hasParameterOption('--shard-index')) {
            $index = $this->input->getParameterOption('--shard-index');
            $total = $this->input->getParameterOption('--shard-total');

            if ($index === null || $total === null) {
                throw new InvalidArgumentException('Invalid sharding format. Use --shard-index=1 --shard-total=5');
            }

            [$index, $total] = [(int) $index, (int) $total];
        } else {
            return false;
        }

        if ($index < 1 || $total < 1 || $index > $total) {
            throw new InvalidArgumentException('Invalid sharding format. Index and total must be positive integers and index must be less than or equal to total.');
        }

        return [$index, $total];
    }

    /**
     * @param  list<string>  $arguments
     * @return list<string>
     */
    private function removeShardingArgs(array $arguments): array
    {
        foreach ($arguments as $key => $argument) {
            if (str_contains($argument, '=')) {
                [$name] = explode('=', $argument);

                if (in_array($name, self::OPTIONS, strict: true)) {
                    unset($arguments[$key]);
                }
            } else {
                $name = $argument;

                if (in_array($name, self::OPTIONS, strict: true)) {
                    unset($arguments[$key]);

                    if (is_numeric($arguments[$key + 1][0] ?? null)) {
                        unset($arguments[$key + 1]);
                    }
                }
            }
        }

        return array_values($arguments);
    }

    /**
     * @template TKey of array-key
     * @template TItem
     *
     * @param  array<TKey, TItem>  $array
     * @param  positive-int  $numberOfGroups
     * @return array<int, array<TKey, TItem>>
     */
    private static function arraySplit(array $array, int $numberOfGroups): array
    {
        return array_chunk($array, max(1, (int) ceil(count($array) / $numberOfGroups)));
    }

    private function buildFilterArgument(mixed $testsToRun): string
    {
        return addslashes(implode('|', $testsToRun));
    }
}
