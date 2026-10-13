<?php

use Pondol\Common\Facades\JsonKeyValue;

if (! function_exists('auth_theme')) {
    function auth_theme($theme = 'user')
    {
        $authCfg = JsonKeyValue::getAsJson('auth');

        switch ($theme) {
            case 'mail':
                $skin = $authCfg->template->mail ?? 'default';

                return 'pondol-auth::templates.mail.'.$skin;

            case 'user':
            default:
                $skin = $authCfg->template->user ?? 'default';

                return 'pondol-auth::templates.views.'.$skin;
        }
    }
}
