<?php
declare(strict_types=1);

namespace Pest\Sharding;

use Symfony\Component\Process\Process;

final class TestFinder
{
    /**
     * @param list<string> $arguments
     * @return list<string>
     */
    public static function allTests(array $arguments): array
    {
        $output = (new Process(['php', 'vendor/bin/pest', '--list-tests', ...$arguments]))->mustRun()->getOutput();

        preg_match_all('/ - (?:P\\\)?(.+)::/', $output, $matches);

        return array_values(array_unique($matches[1]));
    }
}
