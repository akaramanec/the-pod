<?php

namespace src\api;

use backend\modules\bot\models\BotLogger;
use backend\modules\rent\models\Move;
use Exception;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use src\helpers\Date;
use Yii;

/**
 * https://developers.google.com/calendar/create-events#php
 * https://developers.google.com/calendar/v3/reference/events/insert
 * https://developers.google.com/resources/api-libraries/documentation/calendar/v3/php/latest/
 * @var $move \backend\modules\rent\models\Move
 * @var $client Google_Client
 */
class GoogleCalendar
{
    public $client;
    public $service;
    public $results;
    public $calendarId = 'zodiactelegrambot@gmail.com';
    public $move;
    public $colorId = 9;

    public function __construct()
    {
        $this->client = $this->getClient();
        $this->service = new Google_Service_Calendar($this->client);
    }

    public function get()
    {
        try {
            $this->results = $this->service->events->get($this->calendarId, $this->order->calendarEventId);
            BotLogger::save_input($this->results);
            return $this->results->getId();
        } catch (\Exception $e) {
            return null;
        }
    }

    //php yii cron/index
    public function insert()
    {
        $event = new Google_Service_Calendar_Event(array(
            'summary' => 'Заказ ID: ' . $this->order->id,
            'id' => $this->order->calendarEventId,
            'colorId' => $this->colorId,
            'description' => Yii::$app->urlManager->createAbsoluteUrl(['/rent/move/update', 'id' => $this->order->id]) . PHP_EOL . Yii::$app->urlManager->createAbsoluteUrl(['/rent/move/pdf', 'id' => $this->order->id]),
            'start' => array(
                'dateTime' => Date::formatGoogle($this->order->start_at),
                'timeZone' => 'Europe/Kyiv',
            ),
            'end' => array(
                'dateTime' => Date::formatGoogle($this->order->end_at),
                'timeZone' => 'Europe/Kyiv',
            ),
            'recurrence' => array(
                'RRULE:FREQ=DAILY;COUNT=1'
            ),
        ));

        $event = $this->service->events->insert($this->calendarId, $event);
        return $event->htmlLink;
    }

    public function update()
    {
        $event = new Google_Service_Calendar_Event(array(
            'summary' => 'Заказ ID: ' . $this->order->id,
            'colorId' => $this->colorId,
            'id' => $this->order->calendarEventId,
            'start' => array(
                'dateTime' => Date::formatGoogle($this->order->start_at),
                'timeZone' => 'Europe/Kyiv',
            ),
            'end' => array(
                'dateTime' => Date::formatGoogle($this->order->end_at),
                'timeZone' => 'Europe/Kyiv',
            ),
            'recurrence' => array(
                'RRULE:FREQ=DAILY;COUNT=1'
            ),
        ));

        $event = $this->service->events->update($this->calendarId, $this->order->calendarEventId, $event);
        return $event->htmlLink;
    }

    public function delete()
    {
        $this->results = $this->service->events->delete($this->calendarId, $this->order->calendarEventId);
        BotLogger::save_input($this->results);
    }

    public function colors()
    {
        $this->results = $this->service->colors->get();
        foreach ($this->results->getEvent() as $key => $color) {
            echo "colorId : {$key}<br>";
            echo '<div style="background-color: ' . $color->getBackground() . ';width: 30px; height: 30px;"></div>';
            echo "Background: {$color->getBackground()}<br>";
            echo "Foreground: {$color->getForeground()}<hr>";
        }
        BotLogger::save_input($this->results);
    }

    public function listEvents()
    {
        $optParams = [
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        ];
        $this->results = $this->service->events->listEvents($this->calendarId, $optParams);
        return $this->results->getItems();
    }

    private function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Calendar API PHP Quickstart');
        $client->setScopes(Google_Service_Calendar::CALENDAR);
        $client->setAuthConfig(Yii::getAlias('@backend/modules/bot/src/client_secret.json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        $tokenPath = Yii::getAlias('@backend/modules/bot/src/token.json');
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }
        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                if (php_sapi_name() != 'cli') {
                    exit('Это приложение должно быть запущено из командной строки.');
                }
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }

    public function setMove($move)
    {
        if ($move && $move->start_at && $move->end_at) {
            if ($move->status == Move::STATUS_RESERVE) {
                $this->colorId = 6;
            }
            if ($move->status == Move::STATUS_CONFIRMED) {
                $this->colorId = 10;
            }
            return $this->order = $move;
        }
        throw new \Exception('Нет необходимых параметров');
    }
}
