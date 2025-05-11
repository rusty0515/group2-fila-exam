<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use Filament\Tables\Columns\TextColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Users Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    Section::make('User Details')
                    ->description('The user\'s name and email address.')
                    ->schema([
                        TextInput::make('name')
                        ->required()
                        ->label('User Name')
                        ->maxLength(255),

                        TextInput::make('email')
                        ->required()
                        ->email()
                        ->rules([ 'regex:/^c\d{8}\.[a-z]+@csav\.edu\.ph$/i',
                             'unique:users,email'
                        ])
                        ->validationMessages([
                            'regex' => 'The email must follow the format like c202227799.delacruz@csav.edu.ph.',
                        ])
                        ->unique(ignoreRecord: true),

                        TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                        ->required(fn (Page $livewire): bool => $livewire instanceof Pages\EditUser)
                        ->visible(fn (Page $livewire): bool => $livewire instanceof Pages\CreateUser),

                        TextInput::make('password_confirmation')
                        ->label('Confirm Password')
                        ->password()
                        ->revealable()
                        ->required(fn (Page $livewire): bool => $livewire instanceof Pages\EditUser)
                        ->visible(fn (Page $livewire): bool => $livewire instanceof Pages\CreateUser),
                    ])
                    ->columns(1),

                Section::make('User Profile')
                ->description('The user\'s profile. Input First name and your last name')
                ->relationship('userProfile')
                ->schema([
                    TextInput::make('first_name')
                    ->required()
                    ->minLength(3)
                    ->label('First Name')
                    ->maxLength(255),

                    TextInput::make('middle_initial')
                    ->maxLength(1)
                    ->label('Middle Initial'),
                   

                    TextInput::make('last_name')
                    ->required()
                    ->minLength(3)
                    ->label('Last Name')
                    ->maxLength(255)
                ])

                ])
                ->columns([
                    'sm' => 1,
                    'md' => 2,
                ])
                ->columnSpanFull(),

                Section::make('Roles')
                ->description('Select roles for this user')
                ->schema([
                    CheckboxList::make('roles')
                    ->label('Select Roles')
                    ->relationship(name: 'roles', titleAttribute: 'name')
                    ->searchable()
                    ->columns(2)
                    ->options(function () {
                        return Role::all()->mapWithKeys(function ($role) {
                            return [$role->id => Str::replace('_', ' ', Str::ucwords($role->name))];
                        });
                    })
                ])->columnSpanFull()
            ]);
            
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable()->label('User name'),
                TextColumn::make('email')->sortable()->searchable()->label('Email'),
                TextColumn::make('roles.name')->searchable()->label('Roles')->badge()
                ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('userProfile.full_name')->searchable()->label('Full name')
                ->formatStateUsing(fn ($state) => ucwords($state)),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->tooltip('Actions')
               
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
