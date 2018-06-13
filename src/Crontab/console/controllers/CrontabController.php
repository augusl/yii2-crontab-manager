<?php


namespace console\controllers;

use Crontab\models\CronConfig;
use yii\console\Controller;

class CrontabController extends Controller
{
    /**
     * php yii crontab/crontab
     * 定时任务执行入口
     */
    public function actionCrontab()
    {
        CronConfig::run();
    }


}