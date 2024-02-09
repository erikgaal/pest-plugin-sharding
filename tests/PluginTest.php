<?php

declare(strict_types=1);

use Pest\Sharding\Plugin;
use Pest\Sharding\Tests\FakeTestFinder;
use Symfony\Component\Console\Input\ArrayInput;

it('does nothing without shard argument', function () {
    $plugin = (new Plugin(
        new ArrayInput([]),
        (new FakeTestFinder([
            'Test\A',
            'Test\B',
        ]))
    ));

    expect($plugin->handleArguments([]))
        ->not->toContain('--filter');
});

it('adds a filter argument', function () {
    $plugin = (new Plugin(
        new ArrayInput(['--shard' => '1/2']),
        (new FakeTestFinder([
            'Test\A',
            'Test\B',
        ]))
    ));

    expect($plugin->handleArguments([]))
        ->toContain('--filter', 'Test\\\\A');
});

it('uses alternative arguments to add filter argument', function () {
    $plugin = (new Plugin(
        new ArrayInput(['--shard-index' => '1', '--shard-total' => '2']),
        (new FakeTestFinder([
            'Test\A',
            'Test\B',
        ]))
    ));

    expect($plugin->handleArguments([]))
        ->toContain('--filter', 'Test\\\\A');
});
