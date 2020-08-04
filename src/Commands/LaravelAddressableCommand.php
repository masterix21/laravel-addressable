<?php

namespace Masterix21\LaravelAddressable\Commands;

use Illuminate\Console\Command;

class LaravelAddressableCommand extends Command
{
    public $signature = 'laravel-addressable';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
