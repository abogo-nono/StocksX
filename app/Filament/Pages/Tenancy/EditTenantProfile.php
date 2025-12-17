<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Tenant;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile as BasePage;

class EditTenantProfile extends BasePage
{
    public static function getLabel(): string
    {
        return 'Team profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Company Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Company Email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->maxLength(15),

                Textarea::make('address')
                    ->label('Company Address')
                    ->rows(3)
                    ->maxLength(500),

                FileUpload::make('logo')
                    ->label('Company Logo')
                    ->image()
                    ->disk('public')
                    ->directory('tenants/logos')
                    ->visibility('public')
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                    ->maxSize(2048), // 2MB max

                ColorPicker::make('main_color')
                    ->label('Brand Color')
                    ->default('#0891b2'),
            ]);
    }
}
