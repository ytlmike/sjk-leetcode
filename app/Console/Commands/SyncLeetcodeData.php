<?php

namespace App\Console\Commands;

use App\Services\LeetcodeService;
use Illuminate\Console\Command;

class SyncLeetcodeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leetcode:sync';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $leetcode = new LeetcodeService();
        $leetcode->saveQuestions();
        $leetcode->saveTags();
    }
}
