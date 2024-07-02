<?php

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Unswer\Client;
use Unswer\Exceptions\UnswerException;
use Unswer\Models\Message;
use Unswer\Models\Room;
use Unswer\Services\MessageService;

final class MessageTest extends TestCase
{
    private MessageService $service;
    private static $room;

    public function __construct()
    {
        parent::__construct();

        $client = new Client(null, null, [
            'host' => 'http://localhost:8081/api',
        ]);
        $this->service = $client->messages();
    }

    public function testCanGetRooms()
    {
        $rooms = $this->service->all(1, 10);
        self::$room = $rooms->first();

        // TODO: assert equals from send message method
        $this->assertInstanceOf(Collection::class, $rooms);
        $this->assertInstanceOf(Room::class, self::$room);
        $this->assertIsString(self::$room->getId());
        $this->assertIsInt(self::$room->getPhone());
        $this->assertIsString(self::$room->getTag());
        $this->assertIsBool(self::$room->isBlocked());
        $this->assertInstanceOf(Message::class, self::$room->getLastest());
    }

    /**
     * @depends testCanGetRooms
     */
    public function testCanGetMessages()
    {
        $messages = $this->service->list(self::$room->getId());
        $message = $messages->first();

        // TODO: assert equals from send message method
        $this->assertInstanceOf(Collection::class, $messages);
        $this->assertIsString($message->getId());
        $this->assertIsString($message->getType());
        $this->assertIsObject($message->getBody());
        $this->assertNull($message->getAttachmentUrl());
        $this->assertIsString($message->getStatus());
        $this->assertIsBool($message->isMe());
        $this->assertIsString($message->getReceivedAt());
    }

    public function testRoomWithInvalidLimit()
    {
        $this->expectException(UnswerException::class);
        $this->service->all(['limit' => 80]);
    }

    public function testMessageWithInvalidLimit()
    {
        $this->expectException(UnswerException::class);
        $this->service->list(self::$room->getId(), ['limit' => 80]);
    }
}