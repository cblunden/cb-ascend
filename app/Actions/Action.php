<?php

namespace App\Actions;

use App\Models\User;
use App\Traits\StaticallyConstructable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

abstract class Action
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, StaticallyConstructable;

    public array $attributes;

    private ?User $user = null;
    private ?Validator $validator = null;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function handle()
    {
        $this->resolveAuthorization();
        $this->resolveValidation();

        return $this->run();
    }

    public function all(): array
    {
        return $this->attributes;
    }

    public function set($attribute, mixed $value): static
    {
        Arr::set($this->attributes, $attribute, $value);

        return $this;
    }

    public function get($attribute, mixed $default = null): mixed
    {
        return Arr::get($this->attributes, $attribute, $default);
    }

    public function __get(string $attribute)
    {
        return $this->get($attribute);
    }

    public function user(): User
    {
        return $this->user ?? Auth::user();
    }

    public function actingAs($user): static
    {
        $this->user = $user;

        return $this;
    }

    protected function resolveAuthorization(): void
    {
        throw_if(method_exists($this, 'authorize') && ! $this->authorize(), new AuthorizationException());
    }

    protected function resolveValidation(): void
    {
        if (method_exists($this, 'rules')) {
            Validator::make(
                $this->attributes,
                $this->rules(),
                method_exists($this, 'messages') ? $this->messages() : []
            )->validate();
        }
    }

    abstract protected function run();
}
