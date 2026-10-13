<?php

namespace Pondol\Auth\Traits\Admin;

use Illuminate\Support\Facades\File;
use Pondol\Common\Facades\JsonKeyValue;

trait Config
{
    public function getConfig()
    {
        $user = JsonKeyValue::getAsArray('auth');

        // 템플릿 스킨 스캔 (패키지 내장 스킨 + 프로젝트 vendor 스킨 병합)
        $templates = [
            'user' => $this->scanTemplateDirectories('views'),
            'mail' => $this->scanTemplateDirectories('mail'),
        ];

        return [
            'user' => $user,
            'templates' => $templates,
        ];
    }

    /**
     * 템플릿 디렉토리 스캔 및 병합
     *
     * @param  string  $type  'views' | 'mail'
     */
    protected function scanTemplateDirectories(string $type): array
    {
        $skins = [];

        // 1. 패키지 내장 기본 스킨 디렉토리
        $packageDir = __DIR__.'/../../resources/views/templates/'.$type;
        if (File::isDirectory($packageDir)) {
            $skins = array_merge($skins, array_map('basename', File::directories($packageDir)));
        }

        // 2. 라라벨 표준 프로젝트 오버라이딩 디렉토리 (resources/views/vendor/pondol-auth/templates/...)
        $projectVendorDir = resource_path('views/vendor/pondol-auth/templates/'.$type);
        if (File::isDirectory($projectVendorDir)) {
            $skins = array_merge($skins, array_map('basename', File::directories($projectVendorDir)));
        }

        // 3. 중복 제거 및 이름순 정렬
        $skins = array_unique($skins);
        sort($skins);

        return $skins;
    }

    public function _update($request)
    {
        $config = [
            'activate' => $request->activate,
            'template' => [
                'user' => $request->t_user,
                'mail' => $request->t_mail,
            ],
            'point' => [
                'register' => $request->input('r_point', 0),
                'login' => $request->input('l_point', 0),
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
