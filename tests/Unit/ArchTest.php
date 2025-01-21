<?php

declare(strict_types=1);

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

arch()->preset()->php();
arch()->preset()->laravel();
arch()->preset()->security();

arch('strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('exceptions')
    ->expect('App\Exceptions')
    ->toImplement('Throwable');

arch('avoid mutation')
    ->expect('App')
    ->classes()
    ->toBeReadonly()
    ->ignoring([
        'App\Exceptions',
        'App\Jobs',
        'App\Models',
        'App\Providers',
        'App\Services',
    ]);

arch('avoid inheritance')
    ->expect('App')
    ->classes()
    ->toExtendNothing()
    ->ignoring([
        'App\Models',
        'App\Exceptions',
        'App\Jobs',
        'App\Providers',
        'App\Services',
    ]);

arch('annotations')
    ->expect('App')
    ->toHavePropertiesDocumented()
    ->toHaveMethodsDocumented();

arch('avoid open for extension')
    ->expect('App')
    ->classes()
    ->toBeFinal();

arch('avoid abstraction')
    ->expect('App')
    ->not->toBeAbstract();

arch('enums')
    ->expect('App\Enums')
    ->toBeEnums();

arch('factories')
    ->expect('Database\Factories')
    ->toExtend(Factory::class)
    ->toHaveMethod('definition')
    ->toOnlyBeUsedIn([
        'App\Models',
    ]);

arch('globals')
    ->expect(['dd', 'dump', 'ray', 'die', 'var_dump', 'sleep'])
    ->not->toBeUsed();

arch('jobs')
    ->expect('App\Jobs')
    ->toHaveMethod('handle')
    ->toHaveConstructor()
    ->toImplement(ShouldQueue::class);

arch('models')
    ->expect('App\Models')
    ->toHaveMethod('casts')
    ->toExtend(Model::class)
    ->toOnlyBeUsedIn([
        'App\Http',
        'App\Jobs',
        'App\Models',
        'App\Providers',
        'App\Actions',
        'App\Services',
        'Database\Factories',
        'Database\Seeders', // will be removed in the next PR as removing it gets outside of this PR scope
    ]);

arch('providers')
    ->expect('App\Providers')
    ->toExtend(ServiceProvider::class)
    ->not->toBeUsed();

arch('actions')
    ->expect('App\Actions')
    ->toHaveMethod('handle');
