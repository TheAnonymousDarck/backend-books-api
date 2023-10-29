<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BooksApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_all_books()
    {
        $books = Book::factory(4)->create();

        $response = $this->getJson(route('books.index'));

        $response->assertJsonFragment([
            'title' => $books[0]->title,
        ])->assertJsonFragment([
            'title' => $books[1]->title,
        ]);
    }

    /** @test */
    public function can_get_one_books()
    {
        $book = Book::factory()->create();

        $response = $this->getJson(route('books.show', $book));

        $response->assertJsonFragment([
            'title' => $book->title,
        ]);
    }

    /** @test */
    public function can_create_books()
    {
        $this->postJson(route('books.store', []))
            ->assertJsonValidationErrorFor('title');

        $book = [
            'title' => 'New Book Test',
        ];

        $this->postJson(route('books.store', $book))
            ->assertJsonFragment([
                'title' => $book['title'],
            ]);

        $this->assertDatabaseHas('books', [
            'title' => $book['title'],
        ]);
    }

    /** @test */
    public function can_update_books()
    {
        $book = Book::factory()->create();

        $this->patchJson(route('books.update', $book), [])
            ->assertJsonValidationErrorFor('title');

        $this->patchJson(route('books.update', $book), [
            'title' => 'Edited Book',
        ])->assertJsonFragment([
            'title' => 'Edited Book',
        ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Edited Book',
        ]);
    }

    /** @test */
    public function can_delete_books()
    {
        $book = Book::factory()->create();

        $this->deleteJson(route('books.destroy', $book))
            ->assertNoContent();

        $this->assertDatabaseCount('books', 0);
    }
}
