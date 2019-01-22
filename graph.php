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
    Visitor\FlagDependencies,
    Visitor\RemoveDependenciesSubGraph,
};

new class extends Main {
    protected function main(Environment $env, OperatingSystem $os): void
    {
        $package = bootstrap(
            $http = $os->remote()->http(),
            $clock = $os->clock()
        );

        $graph = new Graph;
        $visualize = new Visualize;
        $flag = new FlagDependencies($http, $clock);
        $remove = new RemoveDependenciesSubGraph;

        $node = $graph($package);
        $flag($node);
        $remove($node);

        $os
            ->control()
            ->processes()
            ->execute(
                Command::foreground('dot')
                    ->withShortOption('Tsvg')
                    ->withShortOption('o', 'graph.svg')
                    ->withInput(
                        $visualize($node)
                    )
            )
            ->wait();
    }
};
