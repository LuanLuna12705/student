<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('registration form uses the forwarded host when accessed through the local proxy', function () {
    $response = $this->withHeaders([
        'X-Forwarded-Host' => 'events.example.test',
        'X-Forwarded-Proto' => 'https',
    ])->get('/register');

    $response->assertSee('action="https://events.example.test/register"', false);
});

test('new users can register', function () {
    Notification::fake();

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'test@example.com')->firstOrFail();

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
    expect($user->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmail::class);
});
