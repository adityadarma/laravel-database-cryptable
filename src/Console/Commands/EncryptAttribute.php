<?php

namespace AdityaDarma\LaravelDatabaseCryptable\Console\Commands;

use AdityaDarma\LaravelDatabaseCryptable\Facades\Crypt;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EncryptAttribute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypt:encrypt {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt model rows attribute';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelName = $this->argument('model');

        try {
            $model = $this->getModelClass($modelName);
            $datas = $model::get();

            foreach ($datas as $data) {
                $attribute = [];
                foreach ($model->getEncryptableAttributes() as $key) {
                    $value = $data->getOriginal($key);
                    if (! Crypt::isEncrypted($value)) {
                        $attribute[$key] = Crypt::encrypt($value);
                    }
                }

                if (count($attribute)) {
                    DB::table($data->getTable())
                        ->where('id', $data->id)
                        ->update($attribute);
                }
            }

            $this->comment('Encrypt data successfully');
        } catch(Exception $e) {
            $this->comment('Failed to Encrypt data');
        }
    }

    /**
     * Get class model
     *
     * @param string $modelName
     */
    function getModelClass(string $modelName)
    {
        $namespace = 'App\\Models\\';
        $className = $namespace . ucfirst($modelName);

        if (class_exists($className)) {
            return new $className();
        } else {
            throw new Exception("Model class {$className} not found.");
        }
    }
}
