<?php
declare(strict_types=1);

namespace Pest\Sharding\TestFinder;

use Symfony\Component\Process\Process;

final class PestTestFinder implements TestFinder
{
    /**
     * @param list<string> $arguments
     * @return list<string>
     */
    public function allTests(array $arguments): array
    {
        $output = (new Process(['php', 'vendor/bin/pest', '--list-tests', ...$arguments]))->mustRun()->getOutput();

        preg_match_all('/ - (?:P\\\)?(.+)::/', $output, $matches);

        return array_values(array_unique($matches[1]));
    }
}
