<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * @see \App\Http\Controllers\AuthController::register
 */
it('ユーザー登録ができる', function () {
    // Arrange
    $data = [
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

    // Act
    $response = $this->postJson('/api/register', $data);

    // Assert
    $response->assertCreated();
    $response->assertJsonStructure(['message', 'token']);
    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});

/**
 * @see \App\Http\Controllers\AuthController::login
 */
it('ログインに成功するとトークンが返る', function () {
    // Arrange
    $user = User::factory()->create([
        'email' => 'login@example.com',
        'password' => Hash::make('mypassword'),
    ]);

    // Act
    $response = $this->postJson('/api/login', [
        'email' => 'login@example.com',
        'password' => 'mypassword',
    ]);

    // Assert
    $response->assertOk();
    $response->assertJsonStructure(['message', 'token']);
});

/**
 * @see \App\Http\Controllers\AuthController::login
 */
it('ログインに失敗するとエラーが返る', function () {
    // Arrange
    $user = User::factory()->create([
        'email' => 'fail@example.com',
        'password' => Hash::make('correct-password'),
    ]);

    // Act
    $response = $this->postJson('/api/login', [
        'email' => 'fail@example.com',
        'password' => 'wrong-password',
    ]);

    // Assert
    $response->assertStatus(401);
    $response->assertJsonValidationErrors(['email']);
});

/**
 * @see \App\Http\Controllers\AuthController::logout
 */
it('ログアウトするとトークンが無効化される', function () {
    // Arrange
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    // Act
    $response = $this->withToken($token)->postJson('/api/logout');

    // Assert
    $response->assertOk();
    $response->assertJsonFragment(['message' => 'Logged out successfully']);
    expect($user->tokens()->count())->toBe(0);
});
