<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('student profile details can be saved for the extracurricular lookup', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'student_code' => 'SV2026001',
            'major' => 'Công nghệ thông tin',
            'class_name' => 'CNTT-K19',
            'date_of_birth' => '2005-04-12',
            'gender' => 'Nữ',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'student_code' => 'SV2026001',
        'major' => 'Công nghệ thông tin',
        'class_name' => 'CNTT-K19',
        'date_of_birth' => '2005-04-12 00:00:00',
        'gender' => 'Nữ',
    ]);

    $this->actingAs($user->refresh())
        ->get(route('activities.scores'))
        ->assertSee('SV2026001')
        ->assertSee('Công nghệ thông tin')
        ->assertSee('CNTT-K19')
        ->assertSee('12/04/2005');
});

test('student codes must be unique when updating profile details', function () {
    $existingStudent = User::factory()->create(['student_code' => 'SV2026002']);
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from('/profile')
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'student_code' => $existingStudent->student_code,
        ])
        ->assertRedirect('/profile')
        ->assertSessionHasErrors('student_code');
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
