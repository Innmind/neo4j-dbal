<?php
declare(strict_types = 1);

require __DIR__.'/vendor/autoload.php';

use Innmind\CLI\{
    Main,
    Environment,
};
use Innmind\OperatingSystem\OperatingSystem;
use function Innmind\Neo4j\DBAL\bootstrap;
use Innmind\Server\Control\Server\Command;
use Innmind\ObjectGraph\{
    Graph,
    Visualize,
};

new class extends Main {
    protected function main(Environment $env, OperatingSystem $os): void
    {
        $package = bootstrap(
            $os->remote()->http(),
            $os->clock()
        );

        $graph = new Graph;
        $visualize = new Visualize;

        $os
            ->control()
            ->processes()
            ->execute(
                Command::foreground('dot')
                    ->withShortOption('Tsvg')
                    ->withShortOption('o', 'graph.svg')
                    ->withInput(
                        $visualize($graph($package))
                    )
            )
            ->wait();
    }
};
