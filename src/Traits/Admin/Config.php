<?php

namespace Pondol\Auth\Traits\Admin;

use Pondol\Common\Facades\JsonKeyValue;

trait Config
{
    public function getConfig()
    {
        $user = JsonKeyValue::getAsArray('auth');

        // auth templates directory scan
        $templates = [];
        $template_dir = resource_path('views/auth/templates/views');
        // 폴더가 없을 경우 대비
        if (\File::exists($template_dir)) {
            $templates['user'] = array_map('basename', \File::directories($template_dir));
        } else {
            $templates['user'] = [];
        }

        $template_dir = resource_path('views/auth/templates/mail');
        if (\File::exists($template_dir)) {
            $templates['mail'] = array_map('basename', \File::directories($template_dir));
        } else {
            $templates['mail'] = [];
        }

        return [
            'user' => $user,
            'templates' => $templates,
        ];
    }

    public function _update($request)
    {
        // [수정] point 배열에 deduction_priority 추가
        $config = [
            'activate' => $request->activate,
            'template' => [
                'user' => $request->t_user,
                'mail' => $request->t_mail,
            ],
            'point' => [
                'register' => $request->input('r_point', 0),
                'login' => $request->input('l_point', 0),
                // 차감 우선순위 저장 (기본값: free_first)
                'deduction_priority' => $request->input('deduction_priority', 'free_first'),
            ],
        ];

        JsonKeyValue::update('auth', $config);

        return (object) ['error' => false];
    }

    public function getAgreement($key)
    {
        return JsonKeyValue::get($key);
    }

    public function setAgreement($key, $value)
    {
        return JsonKeyValue::set($key, $value);
    }
}
