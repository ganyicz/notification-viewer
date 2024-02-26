## The package

Managing transactional notifications can be a headache. Not just the technical aspect, making sure that the copy is displaying correctly, all links and buttons work and everything is correctly formatted. Some of us even write the copy and want to ensure that the text flows nicely and is consistent with other notifications. And all this needs to be tested.

Solutions like Mailtrap, HELO or even Ray work great for previews but that's all they can do: preview.

This package helps you **manage** notifications in your Laravel application by:

* Testing if a notification can be rendered in all scenarios (using Pest)
* Displaying all your tested notifications in a Web UI
* _Automatically organizing your notifications_
* _Allowing you to setup and preview a custom theme_
* _Using AI to polish the copy of your notifications_

## Setup

This package is test-based - that means you have to define every notifications as a test case.

> [!NOTE]
> _You can use the package:init command to generate the initial tests for all of your existing notifications._

### Manual setup

Create a new Pest test file with the following content:

```php
<?php

use Tests\NotificationSink;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => Notification::swap(new NotificationSink));
afterEach(fn () => Notification::store());

beforeEach()->expectNotToPerformAssertions();
```

This will ensure all tested notifications are stored and that the tests do not require any assertions.

### Creating a notification test

To test a notification, all you have to do is send it to a notifiable. This approach gives you full flexibility to setup the world for each notification to ensure the final preview will match real scenarios. You can also leverage Pest datasets feature to provide different arguments to the notification. Each successful run will be saved as a separate notification preview.

```php
test('user registered', function () {
    $user = User::factory()->create();

    $user->notify(new UserRegistered);
});
```

### Previewing notifications

To generate previews, simply run the tests.

After the tests have finished running, visit `/notification` in your browser to view all generated previews:

<img width="1296" alt="Screenshot 2024-02-26 at 00 32 04" src="https://github.com/ganyicz/notification-viewer/assets/3823354/f38a3541-ce15-403e-b893-da08915d2279">
