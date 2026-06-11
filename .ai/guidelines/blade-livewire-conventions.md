# Blade & Livewire Conventions

This document defines how we write Blade templates and Livewire components in Keystone.
The core principle is simple: **Blade is for display only. All logic lives in the class.**

---

## Blade Templates — What They Should Not Contain

Blade files must never contain the following. If you find them, they are code smells and
should be refactored.

### ❌ Never use `@php` blocks for logic

```blade
{{-- ❌ Wrong --}}
@php
    $user = auth()->user();
    $unread = $user->notifications()->unread()->count();
    $label = $unread > 0 ? "({$unread}) Notifications" : 'Notifications';
@endphp

{{-- ✅ Right: computed property or method on the Livewire component --}}
{{ $this->notificationLabel }}
```

The only acceptable use of `@php` is trivial one-line aliasing purely for template
readability — and even then, ask whether it belongs in the component instead.

### ❌ Never query the database in Blade

```blade
{{-- ❌ Wrong --}}
@foreach(App\Models\Product::active()->get() as $product)

{{-- ✅ Right: passed from the component or computed property --}}
@foreach($this->products as $product)
```

### ❌ Never call facades or service container in Blade

```blade
{{-- ❌ Wrong --}}
@if(Gate::allows('manage-billing'))
@if(app(FeatureService::class)->enabled('new-dashboard'))

{{-- ✅ Right: expose as a boolean property or computed property --}}
@if($this->canManageBilling)
@if($this->newDashboardEnabled)
```

### ❌ Never use `View::share()` or view composers for component-specific data

View composers are a global side-effect. For data that a Livewire component needs,
it belongs in `mount()`, a computed property, or a dependency resolved in the constructor.
View composers are only acceptable for true application-wide globals (e.g. the authenticated
user being available everywhere) and should be rare.

---

## Livewire Component Structure

Every Livewire component follows this ordering convention:

```php
<?php

namespace App\Livewire\Crm\Contacts;

use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ContactList extends Component
{
    // 1. Public properties (bound to the template)
    public string $search = '';
    public string $sortBy = 'name';

    // 2. Form / validation properties grouped together
    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('required|email')]
    public string $email = '';

    // 3. Constructor injection for services
    public function __construct(
        protected ContactService $contactService,
    ) {}

    // 4. mount() — receives route model binding or page-level setup
    public function mount(): void
    {
        // Initialise state that depends on URL params or auth context.
        // Do not query here if a computed property will do.
    }

    // 5. Computed properties — lazy-loaded, cached per request
    #[Computed]
    public function contacts(): Collection
    {
        return Contact::query()
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->orderBy($this->sortBy)
            ->get();
    }

    // 6. Event listeners
    #[On('contact-created')]
    public function refreshList(): void
    {
        unset($this->contacts); // clear computed cache
    }

    // 7. Actions (called from the template via wire:click etc.)
    public function create(): void
    {
        $this->validate();
        $this->contactService->create(['name' => $this->name, 'email' => $this->email]);
        $this->reset('name', 'email');
        $this->dispatch('contact-created');
    }

    public function delete(Contact $contact): void
    {
        $this->authorize('delete', $contact);
        $contact->delete();
    }

    // 8. render() — always last, always thin
    public function render()
    {
        return view('livewire.crm.contacts.contact-list');
    }
}
```

---

## Computed Properties

Use `#[Computed]` for any data the template needs that is derived from state. This replaces:
- `@php` variable assignments in Blade
- Data set in `mount()` that should be reactive
- View composers for component-specific data

```php
// ✅ Computed property — reactive, cached, testable
#[Computed]
public function unreadCount(): int
{
    return auth()->user()->notifications()->unread()->count();
}
```

In the template:

```blade
{{ $this->unreadCount }}
```

Computed properties are automatically cleared when their dependencies change. If you need
to manually bust the cache (e.g. after a mutation), call `unset($this->propertyName)`.

---

## Authorisation

Never check gates or policies in Blade using facades. Expose a computed boolean instead:

```php
#[Computed]
public function canDeleteContacts(): bool
{
    return $this->authorize('delete-contacts');
}
```

```blade
@if($this->canDeleteContacts)
    <button wire:click="delete({{ $contact->id }})">Delete</button>
@endif
```

---

## Passing Data to Child Components / Blade Partials

Child Livewire components receive data via their `mount()` method — never via view composers
or shared state. Plain Blade partials should only receive data that is already resolved:

```blade
{{-- ✅ Pass already-resolved data to a partial --}}
@include('partials.contact-card', ['contact' => $contact])

{{-- ❌ Do not resolve data inside the partial --}}
```

---

## Forms

Use Livewire's `Form` objects for any non-trivial form rather than binding properties
directly on the component. This keeps the component lean and the form logic reusable:

```php
// app/Livewire/Forms/ContactForm.php
class ContactForm extends \Livewire\Form
{
    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('required|email|unique:contacts,email')]
    public string $email = '';

    public function fill(Contact $contact): void
    {
        $this->name = $contact->name;
        $this->email = $contact->email;
    }
}
```

```php
// In the component
public ContactForm $form;

public function save(): void
{
    $this->form->validate();
    Contact::create($this->form->all());
}
```

---

## What Belongs Where — Quick Reference

| Concern | Where it lives |
|---|---|
| Database queries | Computed property or service class |
| Authorisation checks | Computed boolean property |
| Feature flags | Computed boolean property |
| Form state & validation | `Form` object or `#[Rule]` properties |
| Initialisation from URL / route | `mount()` |
| Reactions to events | `#[On]` listener method |
| Shared app-wide data (auth user etc.) | `AppServiceProvider` via `View::share()` — sparingly |
| Business logic | Service class, called from the component action |
| Display formatting | Blade, or a dedicated presenter/cast on the model |

---

## Red Flags to Watch For

If you see any of the following in a Blade or Livewire file, flag it for refactoring:

- `@php` with more than one line or any logic beyond a simple alias
- `App::make()` or `app()` in a Blade file
- `DB::` or any Eloquent query in a Blade file
- `Gate::` or `can()` facade calls directly in Blade (use computed booleans)
- `View::share()` inside a controller or route (should be a service provider, and used sparingly)
- Data being set in `mount()` that never changes — use a computed property
- A component with more than ~150 lines — consider extracting a child component or service
- Always include the render method
