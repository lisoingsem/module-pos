<?php

declare(strict_types=1);

namespace Modules\POS\Http\Middleware;

use App\Data\MenuData;
use App\Enums\MenuGroupEnum;
use App\Services\MenuService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class POSMenuMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        MenuService::addMenuItem(new MenuData(
            group: MenuGroupEnum::DASHBOARD_PRIMARY->value,
            value: 'pos',
            title: __('pos::menu.pos'),
            url: route('dashboard.pos.cashier.index'),
            icon: 'ShoppingCart',
            order: 300,
        ));

        MenuService::addSubmenuItem(new MenuData(
            group: MenuGroupEnum::DASHBOARD_PRIMARY->value,
            value: 'pos',
            title: __('pos::menu.cashier'),
            url: route('dashboard.pos.cashier.index'),
            icon: 'CreditCard',
        ));

        MenuService::addSubmenuItem(new MenuData(
            group: MenuGroupEnum::DASHBOARD_PRIMARY->value,
            value: 'pos',
            title: __('pos::menu.terminals'),
            url: route('dashboard.pos.terminals.index'),
            icon: 'Monitor',
        ));

        MenuService::addSubmenuItem(new MenuData(
            group: MenuGroupEnum::DASHBOARD_PRIMARY->value,
            value: 'pos',
            title: __('pos::menu.shifts'),
            url: route('dashboard.pos.shifts.index'),
            icon: 'Clock',
        ));

        MenuService::addSubmenuItem(new MenuData(
            group: MenuGroupEnum::DASHBOARD_PRIMARY->value,
            value: 'pos',
            title: __('pos::menu.cash-movements'),
            url: route('dashboard.pos.cash-movements.index'),
            icon: 'Wallet',
        ));

        MenuService::addSubmenuItem(new MenuData(
            group: MenuGroupEnum::DASHBOARD_PRIMARY->value,
            value: 'pos',
            title: __('pos::menu.printers'),
            url: route('dashboard.pos.printers.index'),
            icon: 'Printer',
        ));

        return $next($request);
    }
}
