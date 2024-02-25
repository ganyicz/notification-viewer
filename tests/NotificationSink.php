<?php

namespace Tests;

use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Contracts\Notifications\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Support\Facades\File;
use Pest\TestSuite;

class NotificationSink implements Dispatcher, Factory
{
    /**
     * All of the notifications that have been sent.
     *
     * @var array
     */
    protected $notifications = [];

    /**
     * Locale used when sending notifications.
     *
     * @var string|null
     */
    public $locale;

    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param  \Illuminate\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @return void
     */
    public function send($notifiables, $notification)
    {
        $this->sendNow($notifiables, $notification);
    }

    /**
     * Send the given notification immediately.
     *
     * @param  \Illuminate\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @param  array|null  $channels
     * @return void
     */
    public function sendNow($notifiables, $notification, array $channels = null)
    {
        if (! $notifiables instanceof Collection && ! is_array($notifiables)) {
            $notifiables = [$notifiables];
        }

        if (count($notifiables) > 1) {
            throw new \InvalidArgumentException('Only one notifiable is supported.');
        }

        foreach ($notifiables as $notifiable) {
            if (! $notification->id) {
                $notification->id ??= Str::uuid()->toString();
            }

            $notifiableChannels = $channels ?: $notification->via($notifiable);

            if (method_exists($notification, 'shouldSend')) {
                $notifiableChannels = array_filter(
                    $notifiableChannels,
                    fn ($channel) => $notification->shouldSend($notifiable, $channel) !== false
                );
            }

            if (empty($notifiableChannels)) {
                continue;
            }

            $this->notifications[] = [
                'notification' => $notification,
                'channels' => $notifiableChannels,
                'notifiable' => $notifiable,
                'locale' => $notification->locale ?? $this->locale ?? value(function () use ($notifiable) {
                    if ($notifiable instanceof HasLocalePreference) {
                        return $notifiable->preferredLocale();
                    }
                }),
            ];
        }
    }

    /**
     * Get a channel instance by name.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function channel($name = null)
    {
        //
    }

    /**
     * Set the locale of notifications.
     *
     * @param  string  $locale
     * @return $this
     */
    public function locale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Store the sent notifications in storage.
     *
     * @return array
     */
    public function store()
    {
        foreach ($this->notifications as $notification) {
            /** @var \PHPUnit\Framework\TestCase|\Pest\Concerns\Testable */
            $test = TestSuite::getInstance()->test;
            $testMethodName = $test->getPrintableTestCaseMethodName();
            
            $filename = str_replace([' ', '"'], ['_', ''], $testMethodName);

            $html = $notification['notification']->toMail($notification['notifiable'])->render();
            
            File::ensureDirectoryExists(storage_path('notifications'));
            File::put(storage_path("notifications/{$filename}.html"), $html);
        }
    }
}
