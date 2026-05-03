<?php
namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Orders', Order::count())
                ->description('All time')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),

            Stat::make('Pending Orders', Order::where('status', 'pending')->count())
                ->description('Need attention')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Revenue', '$' . number_format(
                Order::where('payment_status', 'paid')->sum('total'), 2
            ))
                ->description('Paid orders only')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Customers', User::where('role', 'customer')->count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Products', Product::count())
                ->description(Product::where('stock', 0)->count() . ' out of stock')
                ->descriptionIcon('heroicon-m-cube')
                ->color('gray'),
        ];
    }
}