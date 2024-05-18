# Laravel Database Cryptable
Laravel Database Encryption to safety your data.

### Laravel Installation Instructions
From your projects root folder in terminal run:

   ```bash
   composer require adityadarma/laravel-database-cryptable
   ```

### Support
- MYSQL
- MariaDB

### Usage

Use the `CryptableAttribute` trait in any Eloquent model that you wish to apply encryption
to and define a `protected $encryptable` array containing a list of the attributes to encrypt.

For example:

```php
    
    use AdityaDarma\LaravelDatabaseCryptable\Traits\CryptableAttribute;

    class User extends Eloquent {
        use CryptableAttribute;
       
        /**
         * The attributes that should be encrypted on save.
         *
         * @var array
         */
        protected $encryptable = [
            'first_name', 'last_name'
        ];
    }
```

By including the `CryptableAttribute` trait, the `setAttribute()`, `getAttribute()` and `getAttributeFromArray()`
methods provided by Eloquent are overridden to include an additional step.

### Searching Encrypted Fields Example:
Searching encrypted field can be done by calling the `whereEncrypted` and `orWhereEncrypted` functions
similar to laravel eloquent `where` and `orWhere`. Ordering encrypted data can be calling `orderByEncrypted` laravel eloquent `orderBy`.


```php
    namespace App\Http\Controllers;

    use App\Models\User;
    class UsersController extends Controller {
        public function index(Request $request)
        {
            $user = User::whereEncrypted('first_name','john')
                        ->orWhereEncrypted('last_name','!=','Doe')
                        ->orderByEncrypted('last_name', 'asc')
                        ->firstOrFail();
            
            return $user;
        }
    }
```

### Encrypt your current data
 If you have current data in your database you can encrypt it with the: 
    `php artisan crypt:encrypt User` command.
    
 Additionally you can decrypt it using the:
    `php artisan crypt:decrypt 'User` command.

 Note: You must implement first the `CryptableAttribute` trait and set `$encryptable` attributes

### Exists and Unique Validation Rules
 If you are using exists and unique rules with encrypted values replace it with exists_encrypted and unique_encrypted 
    ```php
      $validator = validator(['email'=>'foo@bar.com'], ['email'=>'unique_encrypted:users,email']);
    ```

#### Can I encrypt all my `User` model data?
Aside from IDs you can encrypt everything you wan't

For example:
Logging-in on encrypted email
```
$user = User::whereEncrypted('email','test@gmail.com')->filter(function ($item) use ($request) {
        return Hash::check($password, $item->password);
    })->where('active',1)->first();
```

## Credits
This package was inspired from the following:
 [austinheap/laravel-database-encryption](https://github.com/austinheap/laravel-database-encryption)
 [magros/laravel-model-encryption](https://github.com/magros/laravel-model-encryption)
 [DustApplication/laravel-database-model-encryption](https://github.com/DustApplication/laravel-database-model-encryption.git)
 [elgiborsolution/laravel-database-encryption](https://github.com/elgiborsolution/laravel-database-encryption)

## License

This Package is licensed under the MIT license. Enjoy!
