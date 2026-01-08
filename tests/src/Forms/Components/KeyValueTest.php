<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithKeyValue::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithKeyValue::class)
        ->fillForm(['metadata' => ['key1' => 'value1', 'key2' => 'value2']])
        ->assertSchemaStateSet(['metadata' => ['key1' => 'value1', 'key2' => 'value2']]);
});

it('can render when not addable', function (): void {
    livewire(TestComponentWithReadOnlyKeyValue::class)
        ->assertSuccessful();
});

it('can render when reorderable', function (): void {
    livewire(TestComponentWithReorderableKeyValue::class)
        ->assertSuccessful();
});

class TestComponentWithKeyValue extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                KeyValue::make('metadata'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithReadOnlyKeyValue extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                KeyValue::make('metadata')
                    ->addable(false)
                    ->deletable(false),
            ])
            ->statePath('data');
    }
}

class TestComponentWithReorderableKeyValue extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                KeyValue::make('metadata')->reorderable(),
            ])
            ->statePath('data');
    }
}
