<?php

namespace Filament\Tests\Tables\Filters;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render table with `SelectFilter`', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithSelectFilter::class)
        ->assertSuccessful();
});

it('can filter records by relationship', function (): void {
    $author1 = User::factory()->create();
    $author2 = User::factory()->create();

    $postsWithAuthor1 = Post::factory()->count(3)->create(['author_id' => $author1->getKey()]);
    $postsWithAuthor2 = Post::factory()->count(2)->create(['author_id' => $author2->getKey()]);

    livewire(TestTableWithSelectFilter::class)
        ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2))
        ->filterTable('author', $author1->getKey())
        ->assertCanSeeTableRecords($postsWithAuthor1)
        ->assertCanNotSeeTableRecords($postsWithAuthor2);
});

it('can filter records by attribute options', function (): void {
    $postsWithRating1 = Post::factory()->count(2)->create(['rating' => 1]);
    $postsWithRating5 = Post::factory()->count(3)->create(['rating' => 5]);

    livewire(TestTableWithSelectFilter::class)
        ->assertCanSeeTableRecords($postsWithRating1->merge($postsWithRating5))
        ->filterTable('rating', 1)
        ->assertCanSeeTableRecords($postsWithRating1)
        ->assertCanNotSeeTableRecords($postsWithRating5);
});

it('can reset `SelectFilter` to show all records', function (): void {
    $author = User::factory()->create();

    $postsWithAuthor = Post::factory()->count(3)->create(['author_id' => $author->getKey()]);
    $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

    livewire(TestTableWithSelectFilter::class)
        ->filterTable('author', $author->getKey())
        ->assertCanNotSeeTableRecords($postsWithoutAuthor)
        ->resetTableFilters()
        ->assertCanSeeTableRecords($postsWithAuthor->merge($postsWithoutAuthor));
});

it('can filter records with no relationship using `hasEmptyRelationshipOption`', function (): void {
    $author = User::factory()->create();

    $postsWithAuthor = Post::factory()->count(3)->create(['author_id' => $author->getKey()]);
    $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

    livewire(TestTableWithEmptyRelationshipFilter::class)
        ->assertCanSeeTableRecords($postsWithAuthor->merge($postsWithoutAuthor))
        ->filterTable('author', '__empty')
        ->assertCanSeeTableRecords($postsWithoutAuthor)
        ->assertCanNotSeeTableRecords($postsWithAuthor);
});

it('can filter records by specific relationship value using `hasEmptyRelationshipOption`', function (): void {
    $author1 = User::factory()->create();
    $author2 = User::factory()->create();

    $postsWithAuthor1 = Post::factory()->count(3)->create(['author_id' => $author1->getKey()]);
    $postsWithAuthor2 = Post::factory()->count(2)->create(['author_id' => $author2->getKey()]);
    $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

    livewire(TestTableWithEmptyRelationshipFilter::class)
        ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2)->merge($postsWithoutAuthor))
        ->filterTable('author', $author1->getKey())
        ->assertCanSeeTableRecords($postsWithAuthor1)
        ->assertCanNotSeeTableRecords($postsWithAuthor2)
        ->assertCanNotSeeTableRecords($postsWithoutAuthor);
});

it('can filter records with no relationship using `hasEmptyRelationshipOption` with `multiple()`', function (): void {
    $author = User::factory()->create();

    $postsWithAuthor = Post::factory()->count(3)->create(['author_id' => $author->getKey()]);
    $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

    livewire(TestTableWithMultipleEmptyRelationshipFilter::class)
        ->assertCanSeeTableRecords($postsWithAuthor->merge($postsWithoutAuthor))
        ->filterTable('author', ['__empty'])
        ->assertCanSeeTableRecords($postsWithoutAuthor)
        ->assertCanNotSeeTableRecords($postsWithAuthor);
});

it('can filter records by specific relationship values using `hasEmptyRelationshipOption` with `multiple()`', function (): void {
    $author1 = User::factory()->create();
    $author2 = User::factory()->create();
    $author3 = User::factory()->create();

    $postsWithAuthor1 = Post::factory()->count(2)->create(['author_id' => $author1->getKey()]);
    $postsWithAuthor2 = Post::factory()->count(2)->create(['author_id' => $author2->getKey()]);
    $postsWithAuthor3 = Post::factory()->count(2)->create(['author_id' => $author3->getKey()]);
    $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

    livewire(TestTableWithMultipleEmptyRelationshipFilter::class)
        ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2)->merge($postsWithAuthor3)->merge($postsWithoutAuthor))
        ->filterTable('author', [$author1->getKey(), $author2->getKey()])
        ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2))
        ->assertCanNotSeeTableRecords($postsWithAuthor3)
        ->assertCanNotSeeTableRecords($postsWithoutAuthor);
});

it('can filter records by relationship values combined with empty option using `hasEmptyRelationshipOption` with `multiple()`', function (): void {
    $author1 = User::factory()->create();
    $author2 = User::factory()->create();

    $postsWithAuthor1 = Post::factory()->count(2)->create(['author_id' => $author1->getKey()]);
    $postsWithAuthor2 = Post::factory()->count(2)->create(['author_id' => $author2->getKey()]);
    $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

    livewire(TestTableWithMultipleEmptyRelationshipFilter::class)
        ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2)->merge($postsWithoutAuthor))
        ->filterTable('author', ['__empty', $author1->getKey()])
        ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithoutAuthor))
        ->assertCanNotSeeTableRecords($postsWithAuthor2);
});

class TestTableWithSelectFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('author.name'),
                Tables\Columns\TextColumn::make('rating'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('author')
                    ->relationship('author', 'name'),
                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        1 => '1 Star',
                        2 => '2 Stars',
                        3 => '3 Stars',
                        4 => '4 Stars',
                        5 => '5 Stars',
                    ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithEmptyRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('author.name'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('author')
                    ->relationship('author', 'name', hasEmptyOption: true),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithMultipleEmptyRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('author.name'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('author')
                    ->relationship('author', 'name', hasEmptyOption: true)
                    ->multiple(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
