<?php

use yii\db\Migration;

/**
 * Handles the creation of table `message`.
 */
class m170718_053526_create_message_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('message', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
            'homepage' => $this->string(),
            'text' => $this->text()->notNull(),
            'ip' => $this->string()->notNull(),
            'browser' => $this->string()->notNull(),
            'file_url' => $this->string(),
            'created_at' => $this->dateTime()->notNull()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('message');
    }
}
