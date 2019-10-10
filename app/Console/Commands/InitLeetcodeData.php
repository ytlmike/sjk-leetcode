<?php

namespace App\Console\Commands;

use App\Services\Leetcode;
use Illuminate\Console\Command;

class InitLeetcodeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leetcode:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Init Leetcode Questions and Tags Data';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $leetcode = new Leetcode();
        $leetcode->saveQuestions();
        $leetcode->saveTags();
    }
}
