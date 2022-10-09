<?php

namespace App\Bi\Dashboards;

use App\Models\User;
use LaravelBi\LaravelBi\Dashboard;
use LaravelBi\LaravelBi\Dimensions\BelongsToDimension;
use LaravelBi\LaravelBi\Dimensions\StringDimension;
use LaravelBi\LaravelBi\Metrics\AverageMetric;
use LaravelBi\LaravelBi\Metrics\SumMetric;
use LaravelBi\LaravelBi\Widgets\BigNumber;
use LaravelBi\LaravelBi\Filters\DateFilter;
use LaravelBi\LaravelBi\Metrics\CountMetric;
use LaravelBi\LaravelBi\Widgets\LineChart;
use LaravelBi\LaravelBi\Widgets\PartitionPie;
use LaravelBi\LaravelBi\Widgets\Table;

class UserDashboard extends Dashboard
{

    public $model  = User::class;
    public $uriKey = 'users';
    public $name   = 'User dashboard';

    public function filters()
    {
        return [
            DateFilter::create('created_at', 'Created at'),
        ];
    }

    public function widgets()
    {
        return [
            BigNumber::create('user-count', 'Registered users')
                ->metric(
                    CountMetric::create('count', 'Count')
                        ->color('#ff5555')
                )
                ->width('1/6'),

            LineChart::create('user-per-country', 'User per Country')
                ->dimensions([
                    BelongsToDimension::create('idciudad', 'Ciudad')
                        ->relation('ciudad')
                        ->otherColumn('nombre')
                ])
                ->metrics([
                    CountMetric::create('count', 'Count')
                        ->color('#ff5555'),
                    SumMetric::create('id', 'sum')
                        ->color('#cc0055'),
                ])
                ->width('1/3'),

            PartitionPie::create('user-per-country', 'User per Country')
                ->dimensions([
                    BelongsToDimension::create('idciudad', 'Ciudad')
                        ->relation('ciudad')
                        ->otherColumn('nombre')
                ])
                ->metrics([
                    CountMetric::create('count', 'Count')
                        ->color('#ff5555'),
                ])
                ->width('1/3'),

            Table::create('users', 'Table')
                ->dimensions([
                    BelongsToDimension::create('idciudad', 'Ciudad')
                        ->relation('ciudad')
                        ->otherColumn('nombre')
                ])
                ->metrics([
                    CountMetric::create('count', 'Cantinad')
                                ->color('#ff5555'),
                ])
                ->orderBy('count', 'desc')
                ->width('1/3'),
        ];
    }
}
