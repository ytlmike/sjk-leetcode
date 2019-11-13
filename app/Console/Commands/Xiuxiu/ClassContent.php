<?php


namespace App\Console\Commands\Xiuxiu;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClassContent extends Command
{
    protected $signature = 'xiuxiu:content';


    public function handle()
    {
        $count = DB::table('sanjieke_main_beta.class_content_v4')->count();
        $limit = 500;
        $count = ceil($count/$limit);
        $file_question = fopen(storage_path('xiuxiu_class_content_question.csv'), 'a');
        $file_video = fopen(storage_path('xiuxiu_class_content_video.csv'), 'a');

        fputcsv($file_question, ['class_id','question_id','chapter_id','section_id']);
        fputcsv($file_video, ['class_id','video_id','chapter_id','section_id']);

        for($i=0;$i<$count;$i++){
            $start = $limit * $i;
            $list = DB::table('sanjieke_main_beta.class_content_v4')->offset($start)->limit($limit)->get();
            foreach ($list as $item) {
                $classId = $item->class_id;
                $content = json_decode($item->content,true);
                if($content){
                    if(isset($content['questions'])){
                        $questions = $content['questions'];
                        foreach ($questions as $questionId => $question) {
                            foreach ($question as $item) {
                                $current = [
                                    'class_id' => $classId,
                                    'question_id' => $questionId,
                                    'chapter_id' => $item['chapter_id'],
                                    'section_id' => $item['section_id']
                                ];
                                fputcsv($file_question, $current);
                            }
                        }
                    }

                    if(isset($content['videos'])){
                        $videos = $content['videos'];
                        foreach ($videos as $videoId => $video) {
                            foreach ($video as $item) {
                                $current = [
                                    'class_id' => $classId,
                                    'video_id' => $videoId,
                                    'chapter_id' => $item['chapter_id'],
                                    'section_id' => $item['section_id']
                                ];
                                fputcsv($file_video, $current);
                            }
                        }
                    }
                }
            }
        }
    }
}
