<?php

namespace App\Console\Commands;

use App\Models\SjkUser;
use App\Services\Leetcode;
use Illuminate\Console\Command;

class SyncUserQuestion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync User Submit Questions Data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $users = SjkUser::all();
        (new Leetcode())->syncUserSubmit($users);
    }
}
