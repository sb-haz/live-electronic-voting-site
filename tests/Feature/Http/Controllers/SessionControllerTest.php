<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Models\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SessionControllerTest extends TestCase
{
   Use RefreshDatabase;

   public function test_session_page_is_rendered_properly()
   {
       // Create a user
       $user = User::factory()->create();
       // Act as a user
       $this->actingAs($user);

       // Hit the page where sessions are displayed
       // Located at /home route
       $response = $this->get('/modules/1');

       // Assert that we got status 200
       $response->assertStatus(200);
   }

   public function test_teachers_validated_can_see_sessions()
   {
       // Create a fake user
       $user = User::factory()->create();
       $this->actingAs($user);

       // Make GET request to homepage where modules are
       $response = $this->get('/modules/1');

       // Assert that user has access to view page
       $response = assertOk();
   }

   public function test_only_logged_in_user_can_see_sessions_page()
   {
       // Make GET request to sessions page without logged in
       $response = $this->get('/modules/1');

       // Assert we were redirected
       $response->assertStatus(302);

       // Assert we are redirected to right page
       $response->assertRedirect('/login');
   }

   public function test_users_can_create_sessions()
    {
        // Create a user
        $user = User::factory()->create();
        $this->actingAs($user);

        //Create a new module which the session will belong to
        $response = $this->post('/modules', [
            'module_name' => 'FYP',
            'module_code' => 'CS2050'
        ]);

        // Want to hit /sessions with a POST request
        $response = $this->post('/modules', [
            'session_topic' => 'MySQL'
        ]);

        // Assert we were redirected
        $response->assertStatus(302);
        // Assert we are redirected to right page
        $response->assertRedirect('/modules/1/sessions/1');

        // Find the session created
        $module = Session::first();
        // Ensure we only have one session
        // Since nothing was in database before
        $this->assertEquals(1, Session::count());

        // Assert session has the proper data
        $this->assertEquals('MySQL', $module->session_topic);

        // Session belongs to user who created the class
        $this->assertEquals($user->id, $session->user_id);
    }
}
