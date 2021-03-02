<?php

declare(strict_types=1);

namespace Tipoff\Reviews\Tests\Feature\Nova;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tipoff\Reviews\Models\Competitor;
use Tipoff\Reviews\Tests\TestCase;

class CompetitorResourceTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function index()
    {
        Competitor::factory()->count(4)->create();

        $this->actingAs(self::createPermissionedUser('view competitors', true));

        $response = $this->getJson('nova-api/competitors')
            ->assertOk();

        $this->assertCount(4, $response->json('resources'));
    }
}
