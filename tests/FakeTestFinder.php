<?php

declare(strict_types=1);

namespace Pest\Sharding\Tests;

use Pest\Sharding\TestFinder\TestFinder;

final class FakeTestFinder implements TestFinder
{
    /**
     * @param list<string>
     */
    public function __construct(
        private array $tests,
    ) {
    }

    public function allTests(array $arguments): array
    {
        return $this->tests;
    }
}
