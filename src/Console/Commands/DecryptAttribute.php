<?php

namespace AdityaDarma\LaravelDatabaseCryptable\Console\Commands;

use AdityaDarma\LaravelDatabaseCryptable\Facades\Crypt;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DecryptAttribute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypt:decrypt {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Decrypt model rows attribute';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelName = $this->argument('model');

        DB::beginTransaction();
        try {
            $model = $this->getModelClass($modelName);
            $datas = $model::get();

            foreach ($datas as $data) {
                $attribute = [];
                foreach ($model->getEncryptableAttributes() as $key) {
                    $value = $data->getOriginal($key);
                    if (Crypt::isEncrypted($value)) {
                        $attribute[$key] = Crypt::decrypt($value);
                    }
                }

                if (count($attribute)) {
                    DB::table($data->getTable())
                        ->where('id', $data->id)
                        ->update($attribute);
                }
            }

            $this->comment('Decrypt data successfully');
            DB::commit();
        } catch(Exception $e) {
            $this->comment('Failed to decrypt data');
            DB::rollBack();
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
