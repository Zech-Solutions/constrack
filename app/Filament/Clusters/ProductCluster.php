<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class ProductCluster extends Cluster
{
    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getNavigationLabel(): string
    {
        return 'Products';
    }
}
