<?php

namespace ProcessDrive\LaravelFileTranslate;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ProcessDrive\LaravelCloudTranslation\Models\Translations;
use DB;
use Cache;
use Illuminate\Support\Facades\Schema;

class LaravelFileRetriveDbValueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate:dbtofile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Db retrive command';

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
        $trans = $this->getDbValue();
        return;
    }
    
    /**
     * Read the lang folder value and stored in database
     */

    public function getDbValue()
    {
       $get_db_value = (DB::table('translation_db')->select('key','en','no')->get()->groupBY('group')->toArray());

    //    dd($get_db_value);

       
       $table_column = [];

       foreach (Schema::getColumnListing('translation_db') as $value) {
            if($value != 'group' && $value != 'id' && $value != 'key') {
                $table_column[$value] = $value;
            }
       }
    //    dd($table_column);
    }
}