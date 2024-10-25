<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use SplFileInfo;
use Tests\TestCase;

class AddSubsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        // $dir = Process::run('fd . ~/Downloads --maxdepth=1 | fzf')->output();
        $dir = '/Users/mbp-greg/Downloads/Suits.S03.1080p.BluRay.x265-RARBG/';
        $files = collect(File::files($dir))->filter(function (SplFileInfo $file) {
            return $file->getExtension() == 'mp4';
        })->flatten();

        foreach ($files as $file) {
            $file_string  = $file->getFilenameWithoutExtension();

            $sub_file_path = "$dir/Subs/$file_string/2_English.srt";
            $output_dir = "$dir/output";
            $output_path = "$output_dir/$file_string.mkv";

            File::makeDirectory($output_dir, force: true);
            if (File::exists($output_path)) {
                File::delete($output_path);
            }

            // this is faster but it doesn't set the subs as default
            //ffmpeg -i input.mp4 -i subtitles.srt -c:v copy -c:a copy -c:s mov_text output.mp4

            // but converting to mkv does
            Process::run("ffmpeg -i {$file->getRealPath()} -i $sub_file_path -c copy -c:a copy -disposition:s:0 default $output_path");
        }
    }
}
