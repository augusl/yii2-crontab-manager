<?php

use yii\db\Migration;

class m180613_013434_cron_log extends Migration
{
    public function safeUp()
    {

    }

    public function safeDown()
    {
        echo "m180613_013434_cron_log cannot be reverted.\n";

        return false;
    }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tablePrefix = Yii::$app->db->tablePrefix;
        $this->createTable($tablePrefix . 'cron_log', [
            'id' => $this->primaryKey()->comment("主键自增ID"),
            'cron_config_id' => $this->integer()->notNull()->comment("定时任务配置ID"),
            'status' => $this->smallInteger(1)->comment("执行结果，0：正常执行，1：异常退出"),
            'remark' => $this->string()->comment("备注原因"),
            'create_time' => $this->timestamp()->comment("创建时间"),

        ]);


    }

    public function down()
    {
        echo "m180613_013434_cron_log cannot be reverted.\n";

        return false;
    }

}
