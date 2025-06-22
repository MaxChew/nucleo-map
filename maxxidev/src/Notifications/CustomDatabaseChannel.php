<?php

namespace Maxxidev\Notifications;

use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Notification;

class CustomDatabaseChannel extends DatabaseChannel
{
    protected function buildPayload($notifiable, Notification $notification)
    {
        $data = $this->getData($notifiable, $notification);

        return array_merge(
            [
                'id' => $notification->id,
                'url' => $data['url'] ?? null,
                'type' => get_class($notification),
                'data' => $data['data'] ?? [],
                'read_at' => null,
            ],
            $this->prepareMorphColumns($data, 'actor'),
            $this->prepareMorphColumns($data, 'object'),
            $this->prepareMorphColumns($data, 'target'),
            $this->prepareMorphColumns($data, 'sender')
        );
    }

    protected function prepareMorphColumns($data, $attribute)
    {
        return ($model = $data[$attribute] ?? false) ? [
            "{$attribute}_id" => $model->getKey(),
            "{$attribute}_type" => $model->getMorphClass(),
        ] : [];
    }
}
