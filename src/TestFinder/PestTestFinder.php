<?php

declare(strict_types=1);

namespace Pest\Sharding\TestFinder;

use Symfony\Component\Process\Process;

final class PestTestFinder implements TestFinder
{
    /**
     * @param  list<string>  $arguments
     * @return list<string>
     */
    public function allTests(array $arguments): array
    {
        $output = (new Process(['php', ...$this->removeParallelArguments($arguments), '--list-tests']))->mustRun()->getOutput();

        preg_match_all('/ - (?:P\\\)?(.+)::/', $output, $matches);

        return array_values(array_unique($matches[1]));
    }

    /**
     * @param  list<string>  $arguments
     * @return list<string>
     */
    private function removeParallelArguments(array $arguments): array
    {
        return array_filter($arguments, fn ($argument) => ! in_array($argument, ['--parallel', '-p'], strict: true));
    }
}
