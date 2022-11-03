<?php

namespace ProcessDrive\LaravelFileTranslate\jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ProcessDrive\LaravelFileTranslate\CloudTranslate;
use Illuminate\Support\Facades\Schema;
use DB;

class MakeNewLocale implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    public $tries = 3;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $iso_code  = DB::table('translate_language_isocode')->get()->pluck('iso_code')->toArray(); 
        $from_lang = array_search($this->data['from_lang'], $iso_code) ? $this->data['from_lang'] : 'en';
        $to_lang   = $this->data['to_lang'];
        if (!Schema::hasColumn('translation_db', $to_lang)) {
            Schema::table('translation_db', function($table) use ($to_lang) {
                $table->text($to_lang)->after('key')->nullable();
            });
        }
        $data =  DB::table('translation_db')->select('id', 'group', 'key', $this->data['from_lang'])->get();
        $translate = new CloudTranslate();
        foreach ($data as $value) {
            $text = $this->data['from_lang'];
            if (@$value->$text) {
                $result[$to_lang] = $translate->translate($value->$text, $from_lang, $to_lang);
                DB::table('translation_db')->where('id', $value->id)->update($result);
            }
        }
        DB::table('translate_language_isocode')->where('iso_code', $to_lang)->where('used','=',0)->update(['used' => 1]);
    }
}