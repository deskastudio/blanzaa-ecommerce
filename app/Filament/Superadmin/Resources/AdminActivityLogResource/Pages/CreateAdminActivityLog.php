<?php

namespace App\Filament\Superadmin\Resources\AdminActivityLogResource\Pages;

use App\Filament\Superadmin\Resources\AdminActivityLogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAdminActivityLog extends CreateRecord
{
    protected static string $resource = AdminActivityLogResource::class;
}
