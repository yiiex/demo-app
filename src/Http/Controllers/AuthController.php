<?php

namespace App\Http\Controllers;

use App\Form\LoginForm;
use App\Http\Helpers\ResponseHelper;
use App\Models\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiiex\Inertia\Inertia;
use Yiisoft\Http\Status;
use Yiisoft\User\CurrentUser;

final class AuthController
{
    public function login(Inertia $inertia): ResponseInterface
    {
        return $inertia->render('User/Login');
    }

    public function loginPost(ServerRequestInterface $request, ResponseHelper $response, CurrentUser $currentUser): ResponseInterface
    {
        $attributes = $request->getParsedBody();
        $form = new LoginForm();
        $form->setAttributes([
            'email' => $attributes['email'] ?? null,
            'password' => $attributes['password'] ?? null,
            'remember' => $attributes['remember'] ?? false,
        ]);
        if (!$form->validate()) {
            return $response->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $form->getErrors(),
            ], Status::UNPROCESSABLE_ENTITY);
        }
        $form->user->last_login_at = date('Y-m-d H:i:s');
        $form->user->save(false);

        if ($currentUser->login($form->user)) {
            return $response->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $currentUser->getIdentity(),
            ], Status::OK);
        } else {
            return $response->json([
                'success' => false,
                'message' => 'Login failed',
            ], Status::INTERNAL_SERVER_ERROR);
        }
    }

    public function register(Inertia $inertia): ResponseInterface
    {
        return $inertia->render('User/Register');
    }

    public function registerPost(
        ServerRequestInterface $request,
        ResponseHelper         $response,
        CurrentUser            $currentUser,
    ): ResponseInterface
    {
        $attributes = $request->getParsedBody();
        $user = new User('register');
        $user->email = $attributes['email'] ?? null;
        $user->password = $attributes['password'] ?? null;
        $user->password_confirm = $attributes['password_confirm'] ?? null;
        if ($user->validate()) {
            if ($user->withHashedPassword()->save(false) && $currentUser->login($user)) {
                return $response->redirect('/');
            } else {
                return $response->json([
                    'success' => false,
                    'message' => 'Failed to save user',
                    'errors' => $user->getErrors(),
                ], Status::INTERNAL_SERVER_ERROR);
            }
        } else {
            return $response->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $user->getErrors(),
            ], Status::UNPROCESSABLE_ENTITY);
        }
    }

    public function logout(CurrentUser $currentUser, ResponseHelper $response): ResponseInterface
    {
        return $response->json([
            'success' => $currentUser->logout(),
        ]);
    }
}
