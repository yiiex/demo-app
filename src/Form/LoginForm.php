<?php

namespace App\Form;

use App\Models\User;
use Yii1x\ActiveRecord\Model\Model;

class LoginForm extends Model
{
    public ?string $email;
    public ?string $password;
    public bool $remember = false;
    public ?User $user;

    public function rules(): array
    {
        return [
            ['email', 'email'],
            ['email, password', 'required'],
            ['remember', 'boolean'],
            ['email', 'validateUser'],
        ];
    }

    public function attributeNames(): array
    {
        return ['email', 'password', 'remember'];
    }

    public function validateUser(): bool
    {
        if ($this->getErrors()) {
            return false;
        }
        if (!$this->user = User::model()->findByAttributes(['email' => $this->email])) {
            $this->addError('email', 'User not found');
            return false;
        }
        if (!password_verify($this->password, $this->user->password)) {
            $this->addError('password', 'Invalid email or password');
            return false;
        }

        return true;
    }
}
