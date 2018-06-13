<?php

use yii\db\Migration;

class m180613_012027_cron_config extends Migration
{
    public function safeUp()
    {

    }

    public function safeDown()
    {
        echo "m180613_012027_cron_config cannot be reverted.\n";

        return false;
    }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tablePrefix=Yii::$app->db->tablePrefix;
        $this->createTable($tablePrefix.'cron_config', [
            'cron_config_id' => $this->primaryKey()->comment("主键自增ID"),
            'name' => $this->string()->notNull()->unique()->comment("任务名称"),
            'remark' => $this->string()->comment("任务备注"),
            'status' => $this->smallInteger(1)->comment("任务状态，0：正常运行，1：终止运行"),
            'start_time' => $this->timestamp()->defaultValue(NULL)->comment("开始运行时间"),
            'interval_time' => $this->integer()->defaultValue(1)->comment("间隔时间"),
            'type' => $this->integer()->defaultValue(1)->comment("0：间隔时间单位为秒，1：间隔时间单位为分钟，2：间隔时间单位为小时，3：间隔时间单位为天，4：间隔时间单位为周，5：间隔时间单位为月"),
            'path' => $this->string(300)->notNull()->comment("运行程序路径"),
            'module' => $this->string(200)->comment("运行程序路径"),
            'sort' => $this->integer()->comment("排序"),
            'last_run_time' => $this->timestamp()->comment("开始运行时间"),
            'create_time' => $this->timestamp()->comment("创建时间"),
            'update_time' => $this->timestamp()->comment("更新时间"),

        ]);
//        $this->createIndex(
//            'idx-cron_config-status',
//            'cron_config',
//            'status'
//        );
    }

    public function down()
    {
        echo "m180613_012027_cron_config cannot be reverted.\n";

        return false;
    }

}
