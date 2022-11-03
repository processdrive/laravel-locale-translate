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
       $db_value  = $this->tableData();
       $db_column = $this->tableColumn();
       foreach ($db_value as $row_data) {
            foreach ($db_column as $column) {
                $this->writeFile($row_data, $column);
            }
       }
    }

    public function writeFile($row, $column)
    {
        $path = $this->mkdirAndmkFile($row->group, $column);
        dd(require($path));
        // dd($read_file, $row, $column);
    }

    public function mkdirAndmkFile($group, $column)
    {
        $path = resource_path('lang/'.$column);
        if (!is_dir($path)) {
            mkdir($path, 0775);
        }
        chmod($path, 0775);
        if (!file_exists($path.'/'.$group.'.php')) {
            fopen($path.'/'.$group.'.php', 'w');
        }
        return $path.'/'.$group.'.php';
    }

    public function tableData()
    {
       $query = DB::table('translation_db')->select('group', 'key');
       foreach (Schema::getColumnListing('translation_db') as $value) {
            if($value != 'group' && $value != 'id' && $value != 'key' && $value != 'created_at' && $value != 'updated_at' && $value != 'deleted_at') {
                $query = $query->addSelect($value);
            }
       }
       return $query->get()->toArray();
    }

    public function tableColumn()
    {
       $column = [];
       foreach (Schema::getColumnListing('translation_db') as $value) {
            if($value != 'group' && $value != 'id' && $value != 'key' && $value != 'created_at' && $value != 'updated_at' && $value != 'deleted_at') {
                $column[$value] = $value;
            }
       }
       return $column;
    }

}