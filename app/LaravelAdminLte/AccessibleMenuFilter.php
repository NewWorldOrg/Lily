<?php

declare(strict_types=1);

namespace App\LaravelAdminLte;

use App\Auth\AdminUser;
use App\Http\Middleware\Accessible;
use Illuminate\Support\Facades\App;

/**
 * @see https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
 * */
class AccessibleMenuFilter
{
    private Accessible $accessible;

    public function __construct()
    {
        $this->accessible = App::make(Accessible::class);
    }

    /**
     * @param array $item
     * @return array
     */
    public function transform(array $item): array
    {
        /** @var AdminUser $currentUser */
        $currentUser = \Auth::guard('web')->user();
        if (
            isset($item['route'])
            && !$this->accessible->canAccess($currentUser, $item['route'])
        ) {
            $item['restricted'] = true;
        }
        return $item;
    }
}
