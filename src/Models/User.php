<?php

namespace App\Models;

use Yii1x\ActiveRecord\Attributes\{Database, Table};
use Yiisoft\Auth\IdentityInterface;

#[Database('db_main')]
#[Table('user')]
class User extends BaseModel implements IdentityInterface
{
    const string ROLE_ADMIN = 'admin';
    const string ROLE_USER = 'user';

    public ?string $password_confirm = null;
    public string $fullName {
        get => implode(' ', [$this->last_name, $this->first_name]);
    }
    protected array $secureAttributes = ['password'];

    public function rules(): array
    {
        return [
            ['email', 'unique', 'on' => ['insert', 'update', 'register']],
            ['email', 'email', 'on' => ['insert', 'update', 'register']],
            ['password', 'length', 'min' => 6, 'max' => 255, 'on' => ['insert', 'register']],
            ['first_name, last_name, email, role', 'required', 'on' => ['insert', 'update']],
            ['email, password, password_confirm', 'required', 'on' => ['insert', 'register']],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match', 'on' => ['register', 'insert']],
        ];
    }

    public function relations(): array
    {
        return [];
    }

    public function fullName(string $fullName): static
    {
        $alias = $this->tableAlias;
        $this->query->whereRaw("LOWER(CONCAT($alias.first_name, ' ', $alias.last_name)) LIKE :fullName", [
            'fullName' => '%'.mb_strtolower(trim($fullName)).'%',
        ]);
        return $this;
    }

    public function attributeLabels(): array
    {
        return [
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email' => 'E-mail',
            'id' => 'ID',
            'password' => 'Password',
            'role' => 'Role',
            'password_confirm' => 'Confirm Password',
        ];
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function withHashedPassword(?string $password = null): static
    {
        $password ??= $this->password;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    public function can(string|array $role): bool
    {
        return in_array($this->role, (array)$role);
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'fullName' => $this->fullName,
            'avatar' => '/images/avatar.webp',
        ]);
    }
}
