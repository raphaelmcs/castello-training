<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    // Aponta para a visualização Blade que vamos criar a seguir
    protected static string $view = 'filament.pages.auth.login';

    // Avisa o Filament para não usar o quadro padrão e liberar a tela toda
    protected static string $layout = 'filament-panels::components.layout.base';
}