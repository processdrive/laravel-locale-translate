<?php

namespace ProcessDrive\LaravelFileTranslate;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ProcessDrive\LaravelCloudTranslation\Models\Translations;
use DB;
use Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Arr;

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
       foreach ($db_value as $row_no => $row_data) {
            foreach ($db_column as $column) {
                $this->writeFile($row_data, $column);
            }
        $this->info("No of rows $row_no is completed.");
       }
    }

    public function writeFile($row, $column)
    {
        $tex = [];
        $path       = $this->mkdirAndmkFile($row->group, $column);
        $file_data  = require($path);
        if (!is_array($file_data)) {
            $file_data = [];
        }
        if (stripos($row->key, '.')) {
            Arr::set($file_data, $row->key, $row->$column);
        } else {
            $file_data[$row->key] = $row->$column;
        }
        $this->createContent($file_data, $path);
    }

    public function createContent($write_array, $path)
    {
        $array_content = "<?php\n\n return [\n\t".$this->makeFileContent($write_array)."];\n";
        fopen($path, 'w');
        file_put_contents($path, $array_content);
    }

    public function makeFileContent($array, $is_array = false)
    {
        $result = '';
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result .= "\"$key\" => [\n\t". $this->makeFileContent($value, true)."],\n";
            } else {
                $text = addslashes($value);
                $result .= "\"$key\" => \"$text\",\n\t";
            }
        }
        return $result;
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