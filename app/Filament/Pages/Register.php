<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Enums\GenderEnum;
use App\Models\UserProfile;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                Step::make('Personal Information')
                ->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->label('First Name')
                            ->maxLength(255)
                            ->minLength(3),
                        
                        TextInput::make('middle_initial')
                        ->maxLength(1)
                            ->label('Middle Initial (Optional)'),
                            
                            
                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->required()
                            ->minLength(3),

                 ])
                ->columns([
                    'sm' => 1,
                    'md' => 1,
                    'lg' => 1,
                ]),
               

                Step::make('Account Information')
                ->schema([
                    $this->getNameFormComponent()->label('Username'),
                    $this->getEmailFormComponent()
                    ->rules([ 'regex:/^c\d{8}\.[a-z]+@csav\.edu\.ph$/i',
                             'unique:users,email'
                    ])
                    ->validationMessages([
                        'regex' => 'The email must follow the format like c202227799.delacruz@csav.edu.ph.',
                    ]),
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                ])
                ->columns([
                    'sm' => 1,
                    'md' => 1,
                    'lg' => 1,
                ]),
             ])
             
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $sanitizedData = $this->sanitizeData($data);
        //user model
        $user = $this->userData($sanitizedData);

        //user proifle model
        $this->userProfile($user,$sanitizedData);

        $this->assignRoles($user);
        return $user;
    }


    //sanitization
    protected function sanitizeData(array $data):array
    {
        return[
            'first_name' => trim(strip_tags($data['first_name'])),
            'last_name' => trim(strip_tags($data['last_name'])),
            'email' => filter_var($data['email'],FILTER_SANITIZE_EMAIL),
            'password' => $data['password'],
            'middle_initial' =>  trim(strip_tags($data['middle_initial'])),
            'name' => trim(strip_tags($data['name'])),
        ];
    }


    //store user email and password
    protected function userData(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password']
        ]);
    }

    //store user profiles
    protected function userProfile(User $user,array $data):UserProfile
    {
        return UserProfile::create([
           'user_id' => $user->id,
           'first_name' => Str::title($data['first_name']),
           'last_name' => Str::title($data['last_name']),
           'middle_initial' => $data['middle_initial'],
        ]);
    }

    protected function assignRoles(User $user): void
    {
        $role = Role::firstOrCreate([
            'name' => 'guest'
        ]);

        $user->assignRole($role);
    }
    
}
