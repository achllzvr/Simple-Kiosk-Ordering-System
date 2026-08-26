<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_redirects_home_to_ordering(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('ordering.selection'));
    }

    public function test_guest_can_view_ordering_selection(): void
    {
        $response = $this->get(route('ordering.selection'));

        $response->assertOk();
    }
}
