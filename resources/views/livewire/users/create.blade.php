<?php

use App\Models\User;
use Mary\Traits\Toast;
use App\Models\Country;
use App\Models\Language;
use Livewire\Volt\Component;
use Livewire\WithFileUploads; 

new class extends Component {
    
	use Toast, WithFileUploads;

	public User $user;

    #[Rule('required')] 
    public string $name = '';
 
    #[Rule('required|email')]
    public string $email = '';
 
    #[Rule('required|password')]
    public string $password = '';
 
    // Optional
    #[Rule('sometimes')]
    public ?int $country_id = null;

 	#[Rule('nullable|image|max:1024')] 
	public $photo;

	#[Rule('required')]
	public array $my_languages = [];
 
    // Optional 
    #[Rule('sometimes')]
    public ?string $bio = null;

    // We also need this to fill Countries combobox on upcoming form
    public function with(): array 
    {
        return [
            'countries' => Country::all(),
			'languages' => Language::all()
        ];
    }

	public function mount(): void
	{
		//$this->fill($this->user);
		//$this->my_languages = $this->user->languages->pluck('id')->all();

	}

	public function create(): void
	{
		// Validate
		$data = $this->validate([
			'name' => ['string', 'required'],
			'email' => ['email', 'required', 'unique:users, email'],
			'password' => ['required'],
			'bio' => ['nullable']
		]);
 
 		$user = new User;
 
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = $this->password;
        $user->bio = $this->bio;
 
 		$user->save();

        $this->user = User::find($user->id);

		$this->user->languages()->sync($this->my_languages);


		if ($this->photo) {
			$url = $this->photo->store('users', 'public');
			$this->user->update(['avatar' => '/storage/'.$url]);
		}

	
		// You can toast and redirect to any route
		$this->success('User created.', redirectTo: '/users/'.$user->id.'/edit');
	}

}; ?>

<div>
    <x-header title="Create new User" separator />

    <x-form wire:submit="create">

        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info from user" size="text-2xl" />
            </div>
            <div class="col-span-3 grid gap-3">

                <x-file label="Avatar" wire:model="photo" accept="image/png, image/jpeg" crop-after-change>
                    <img src="{{ $user->avatar ?? '/empty-user.jpg' }}" class="h-40 rounded-lg" />
                </x-file>
                <x-input label="Name" wire:model="name" />
                <x-input label="Email" wire:model="email" />
                <x-input label="Password" wire:model="password" type="password" />
            </div>
        </div>

        <hr class="my-5" />

        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Details" subtitle="More about the user" size="text-2xl" />

            </div>
            <div class="col-span-3 grid gap-3">
                <x-select label="Country" wire:model="country_id" :options="$countries" placeholder="---" />

                <x-choices-offline label="My languages" wire:model="my_languages" :options="$languages" searchable />

                <x-editor wire:model="bio" label="Bio" hint="The great biography" />
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" link="/users" />
            <x-button label="create" spinner type="submit" class="btn-primary" />

        </x-slot:actions>
    </x-form>

</div>
