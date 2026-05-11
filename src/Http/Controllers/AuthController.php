<?php

namespace App\Http\Controllers;

use App\Form\LoginForm;
use App\Http\Helpers\ResponseHelper;
use App\Infrastructure\Form\FormData;
use App\Infrastructure\View\NavManager;
use App\Models\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiiex\Inertia\Inertia;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

final class AuthController
{
    public function login(Inertia $inertia, NavManager $nav): ResponseInterface
    {
        $nav->setTitle('Login');
        return $inertia->render('User/Login');
    }

    public function loginPost(
        ServerRequestInterface $request,
        CurrentUser            $currentUser,
        FormData               $form,
        UrlGeneratorInterface  $url,
    ): ResponseInterface
    {
        return $form
            ->setModel(new LoginForm())
            ->setAttributes($request->getParsedBody())
            ->validateWithResponse(function (LoginForm $form) use ($currentUser, $url) {
                if ($success = $currentUser->login($form->user)) {
                    $form->user->last_login_at = date('Y-m-d H:i:s');
                    $form->user->save(false);
                }
                return [
                    'success' => $success,
                    'message' => $success ? 'Login successful' : 'Login failed',
                    'redirectUrl' => $success ? $url->generate('home') : null,
                ];
            });
    }

    public function register(Inertia $inertia, NavManager $nav): ResponseInterface
    {
        $nav->setTitle('Register');
        return $inertia->render('User/Register');
    }

    public function registerPost(
        ServerRequestInterface $request,
        CurrentUser            $currentUser,
        FormData               $form,
        UrlGeneratorInterface  $url,
    ): ResponseInterface
    {
        return $form
            ->setModel(new User('register'))
            ->setAttributes($request->getParsedBody())
            ->validateWithResponse(function (User $user) use ($currentUser, $url) {
                return [
                    'success' => $success = $user->withHashedPassword()->save(false) && $currentUser->login($user),
                    'message' => $success ? 'Welcome aboard! 🎉' : 'Oops! Something went wrong',
                    'redirectUrl' => $success ? $url->generate('home') : null,
                ];
            });
    }

    public function logout(CurrentUser $currentUser, ResponseHelper $response): ResponseInterface
    {
        return $response->json([
            'success' => $currentUser->logout(),
        ]);
    }
}
