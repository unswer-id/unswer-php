<?php

namespace Unswer\Services;

use Unswer\Exceptions\UnswerException;
use Unswer\Models\Room;
use Unswer\Models\Message;
use Unswer\BaseClient;
use Unswer\Models\Pager;

class MessageService extends BaseClient
{
    /**
     * @throws UnswerException
     */
    public function all(int $page = 1, int $limit = 10): Pager
    {
        try {
            $pagination = [
                'page' => $page,
                'limit' => $limit,
            ];

            $validation = self::$validator->validate($pagination, [
                'page' => 'numeric',
                'limit' => 'numeric|max:50',
            ]);

            if ($validation->fails()) {
                $errors = implode(', ', $validation->errors()->all());
                throw new UnswerException('Validation error: ' . $errors);
            }

            $response = self::$http->get('messages/' . self::$appId, $pagination);
            $rooms = array_map(fn ($room) => new Room($room), $response->data);

            return new Pager($rooms, $response->meta, [$this, 'all']);
        } catch (\Exception $e) {
            throw new UnswerException('Error fetching rooms: ' . $e->getMessage());
        }
    }

    /**
     * @throws UnswerException
     */
    public function list(string $roomId, int $page = 1, int $limit = 10): Pager
    {
        try {
            $pagination = [
                'page' => $page,
                'limit' => $limit,
            ];

            $validation = self::$validator->validate($pagination, [
                'page' => 'numeric',
                'limit' => 'numeric|max:50',
            ]);

            if ($validation->fails()) {
                $errors = implode(', ', $validation->errors()->all());
                throw new UnswerException('Validation error: ' . $errors);
            }

            $response = self::$http->get('messages/' . self::$appId . '/' . $roomId, $pagination);
            $messages = array_map(fn ($message) => new Message($message), $response->data);

            return new Pager($messages, $response->meta, [$this, 'list']);
        } catch (\Exception $e) {
            throw new UnswerException('Error fetching messages: ' . $e->getMessage());
        }
    }
}
