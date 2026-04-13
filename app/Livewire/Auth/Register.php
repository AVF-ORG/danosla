<?php

namespace App\Livewire\Auth;

use App\Models\Country;
use App\Models\Region;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;

class Register extends Component
{
    public $step = 1;

    // Form Fields
    #[Validate('required|in:carrier,shipper')]
    public $role = '';

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|email|max:255|unique:users,email')]
    public $email = '';

    #[Validate([
        'required',
        'string',
        'min:8',
        'max:20',
        'confirmed',
        'regex:/[a-z]/',
        'regex:/[A-Z]/',
        'regex:/[0-9]/',
        'regex:/[@#\$%\^&\*\+]/',
    ])]
    public $password = '';

    public $password_confirmation = '';

    #[Validate('required|exists:regions,id')]
    public $region_id = '';

    #[Validate('required|exists:countries,id')]
    public $country_id = '';

    #[Validate('required|string|max:255')]
    public $phone = '';

    #[Validate('required|string|max:255')]
    public $address = '';

    #[Validate('required_if:role,shipper|nullable|exists:sectors,id')]
    public $sector_id = '';

    #[Validate('nullable|url|max:255')]
    public $website = '';

    #[Validate('required|string|max:255')]
    public $company_name = '';

    #[Validate('required|string|max:255')]
    public $company_number = '';

    #[Validate('required|accepted')]
    public $acceptedTerms = false;

    public $locale;

    public function mount()
    {
        $this->locale = app()->getLocale();
    }

    #[Computed]
    public function regions()
    {
        return Region::all();
    }

    #[Computed]
    public function countries()
    {
        if (!$this->region_id) {
            return [];
        }

        return Region::find($this->region_id)?->countries ?? [];
    }

    #[Computed]
    public function sectors()
    {
        return Sector::where('is_active', true)->get();
    }

    public function selectRole($role)
    {
        $this->role = $role;
        $this->step = 2;
    }

    public function updatedRegionId()
    {
        $this->country_id = '';
    }

    public function updatedCountryId($value)
    {
        if (!$value) return;
        
        $country = Country::find($value);
        if ($country && $country->international_code) {
            // Update phone only if it doesn't already have a similar prefix
            if (!$this->phone || !str_contains($this->phone, $country->international_code)) {
                $this->phone = $country->international_code . ' ';
            }
        }
    }

    public function back()
    {
        $this->step = 1;
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'country_id' => $this->country_id,
            'phone' => $this->phone,
            'address' => $this->address,
            'sector_id' => ($this->role === 'shipper') ? $this->sector_id : null,
            'website' => $this->website,
            'company_name' => $this->company_name,
            'company_number' => $this->company_number,
            'status' => User::STATUS_PENDING,
        ]);

        $user->assignRole($this->role);

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
