<?php

namespace ProcessDrive\LaravelFileTranslate;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ProcessDrive\LaravelCloudTranslation\Models\Translations;
use DB;
use Cache;
use Illuminate\Support\Facades\Schema;

class LaravelFileTranslateDbStoreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate:filetodb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'My trans db command';

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
        $trans = $this->generateTrans();
        return;
    }
    
    /**
     * Read the lang folder value and stored in database
     */

    public function generateTrans()
    {
        $scan = scandir(resource_path('lang'));
        foreach($scan as $key => $folder) {
            if (is_dir(resource_path("lang/$folder")) && $key > 1) {
                if (!Schema::hasColumn('translation_db', $folder)) {
                    Schema::table('translation_db', function($table) use ($folder) {
                        $table->text($folder)->after('key')->nullable();
                    });
                }
                $find_iso = DB::table('translate_language_isocode')->where('iso_code',$folder)->where('used','=',0);
                if (@$find_iso->first()) {
                    $find_iso->update(['used' => 1]);
                } else {
                    DB::table('translate_language_isocode')->insert([
                        'iso_code' => $folder,
                        'name' => $folder,
                        'used' => 1
                    ]);
                }
                $files = collect(File::files(resource_path('lang/'.$folder)));
                $trans = $files->reduce(function($trans, $file) use ($folder) {
                    $filename = pathinfo($file)['filename'];                    
                    $translations = require($file);
                    if (!array_key_exists($folder, $trans)) {
                        $trans[$folder] = [];
                    }
                    foreach($translations as $key =>  $translation) {
                        if (is_array($translation)) {
                            $this->speratedByArray($key, $translation, $filename, $folder);
                        } else {
                            $data =  DB::table('translation_db')->where('group', $filename)->where('key',$key)->first(); 
                            if(@$data) {
                                DB::table('translation_db')->whereId($data->id)->update([$folder => $translation]);
                            }
                            else {
                                DB::table('translation_db')->insert([
                                    'group' => $filename,
                                    'key' => $key,
                                    $folder => $translation,
                                ]);
                            }
                        }
                    }
                    $filelist = resource_path("lang/$folder").'/'.$filename.'.php';    
                    $this->info("Write trans to: $filelist");
                    return $trans;
                }, []);
            }
        }
    }

    public function speratedByArray($key, $array, $group, $lang) {
        foreach ($array as $array_key => $array_value) {
            $key = $key.'.'.$array_key;
            if (is_array($array_value)) {
                $this->speratedByArray($key, $array_value, $group, $lang);
            } else {
                $data =  DB::table('translation_db')->where('group', $group)->where('key',$key)->first(); 
                if(@$data) {
                    DB::table('translation_db')->whereId($data->id)->update([$lang => $array_value]);
                }
                else {
                    DB::table('translation_db')->insert([
                        'group' => $group,
                        'key' => $key,
                        $lang => $array_value,
                    ]);
                }
            }
        }
    }
}