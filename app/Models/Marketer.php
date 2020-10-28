<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marketer extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'user_id', 'name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updateWithRelations(array $data)
    {
        $userData = [
            "account" => $data["user"]["account"],
            "email" => $data["user"]["email"],
            "phone" => $data["user"]["phone"],
        ];

        if (!empty($data["user"]["password"])) {
            $userData["password"] = bcrypt(trim($data["user"]["password"]));
            $userData["open_password"] = $data["user"]["password"];
        }

        $user = $this->user->update($userData);

        $this->update(["name" => $data["name"]]);

        note("info", "marketer:update", "Обновлены данные маркетолога {$this->name}", self::class, $this->id);

        $this->fresh();

        return $this;
    }

    public static function createWithRelations(array $data)
    {
        $role = Role::findByCode("marketer");

        $user = User::create([
            "account" => $data["user"]["account"],
            "email" => $data["user"]["email"],
            "phone" => $data["user"]["phone"],
            "password" => $data["user"]["password"],
            "open_password" => $data["user"]["password"],
            "role_id" => $role->id
        ]);

        $marketer = self::create([
            "user_id" => $user->id,
            "name" => $data["name"],
        ]);

        note("info", "marketer:create", "Создан новый оператор {$marketer->name}", self::class, $marketer->id);

        return $marketer;
    }
}
