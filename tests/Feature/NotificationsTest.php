<?php

use App\Models\User;
use App\Notifications\UserRegistered;
use Illuminate\Auth\Notifications\ResetPassword;
use Tests\NotificationSink;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

beforeEach(fn () => Notification::swap(new NotificationSink));
afterEach(fn () => Notification::store());

beforeEach()->throwsNoExceptions();

test('user registered', function () {
    $user = User::factory()->create();

    $user->notify(new UserRegistered);
});

test('reset password', function () {
    $user = User::factory()->create();

    $user->notify(new ResetPassword(token: Str::random()));
});

test('verify email', function () {
    $user = User::factory()->create();

    $user->notify(new ResetPassword(token: Str::random()));
});
