<?php

use backend\modules\bot\models\BotMenuCommand;
use yii\db\Migration;

/**
 * Class m211018_180755_init_database_by_referral
 */
class m211018_180755_init_database_by_referral extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $botMenuButchParams = [];

        foreach ($this->newMenuButtons() as $command => $button) {
            $newBotMenu = new BotMenuCommand;
            $newBotMenu->name = $command;
            if ($newBotMenu->save()) {
                $botMenuButchParams[] = [$newBotMenu->id, $button['name'], $button['slug']];
            }
        }

        $this->batchInsert('{{%bot_menu}}', ['command_id', 'name', 'slug'], $botMenuButchParams);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%bot_menu}}', ['slug' => array_map(function ($item) {
            return $item['slug'];
        }, $this->newMenuButtons())]);
        $this->delete('{{%bot_menu_command}}', ['name' => array_keys($this->newMenuButtons())]);
    }

    private function newMenuButtons(): array
    {
        return [
            '/TCabinet_getInfo' => ['name' => 'Кабинет', 'slug' => 'cabinet'],
        ];
    }



}
