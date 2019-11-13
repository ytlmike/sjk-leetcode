<?php


namespace App\Console\Commands\Xiuxiu;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClassStudtyStage extends Command
{
    protected $signature = 'xiuxiu:stage';

    public function handle()
    {
        $file = fopen(storage_path('xiuxiu_class_study_stage.csv'), 'a');
        fputcsv($file, ['class_id', 'start_time', 'end_time', 'stage_order', 'chapter_id', 'section_id']);
        $data = DB::table('sanjieke_main_beta.class_study_stage')->get();
        foreach ($data as $datum) {
            $nodes = json_decode($datum->nodes, true);
            if ($nodes) {
                foreach ($nodes as $chapterId => $nodeList) {
                    foreach ($nodeList as $node) {
                        $current = [
                            'class_id' => $datum->class_id,
                            'start_time' => $datum->start_time,
                            'end_time' => $datum->end_time,
                            'stage_order' => $datum->stage_order,
                            'chapter_id' => $chapterId,
                            'section_id' => $node
                        ];
                        fputcsv($file, $current);
                    }
                }
            }
        }
    }
}
