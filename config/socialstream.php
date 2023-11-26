<?php

use JoelButcher\Socialstream\Features;
use JoelButcher\Socialstream\Providers;

return [
    'middleware' => ['web'],
    'prompt' => 'O inicie sesión a través de',
    'providers' => [
        Providers::facebook(label: 'Continuar con Facebook'),
    ],
    'features' => [
        // Features::createAccountOnFirstLogin(),
        // Features::generateMissingEmails(),
        Features::rememberSession(),
        Features::providerAvatars(),
        Features::refreshOAuthTokens(),
    ],
];
