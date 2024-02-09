<?php

declare(strict_types=1);

namespace Pest\Sharding\TestFinder;

interface TestFinder
{
    /**
     * @param  list<string>  $arguments
     * @return list<string>
     */
    public function allTests(array $arguments): array;
}
