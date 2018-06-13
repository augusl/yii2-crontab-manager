<?php

namespace Crontab\models;


/**
 * This is the model class for table "{{%cron_config}}".
 *
 * @property integer $cron_config_id
 * @property string $name
 * @property string $remark
 * @property integer $status
 * @property string $start_time
 * @property integer $interval_time
 * @property integer $type
 * @property string $path
 * @property string $module
 * @property string $create_time
 * @property string $last_run_time
 * @property string $update_time
 */
class CronConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cron_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['status', 'interval_time', 'type', 'sort'], 'integer'],
            [['start_time', 'create_time'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['remark'], 'string', 'max' => 255],
            [['path'], 'string', 'max' => 300],
            [['module'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cron_config_id' => 'Cron Config ID',
            'name' => 'Name',
            'remark' => 'Remark',
            'status' => 'Status',
            'start_time' => 'Start Time',
            'interval_time' => 'Interval Time',
            'type' => 'Type',
            'path' => 'Path',
            'module' => 'Module',
            'sort' => 'Sort',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCronLogs()
    {
        return $this->hasMany(CronLog::className(), ['cron_config_id' => 'cron_config_id']);
    }

    /**
     * @param array $condition
     * @param string $fields
     * @return array|\yii\db\ActiveRecord[]
     * 根据条件获取任务列表
     */
    public static function getAllList($condition = ["status" => 0], $fields = "*")
    {

        return CronConfig::find()->where($condition)->orderBy(["sort" => SORT_DESC, "start_time" => SORT_ASC, "cron_config_id" => SORT_ASC])->select($fields)->asArray()->all();

    }

    /**
     * @param $cron_config_id
     * @param string $fields
     * @return array|null|\yii\db\ActiveRecord
     * 根据id获取任务详情
     */
    public static function getCrontabConfigById($cron_config_id, $fields = "*")
    {
        return CronConfig::find()->select($fields)->where(["cron_config_id" => $cron_config_id])->one();
    }


    /**
     * 执行任务入口
     */
    public static function run()
    {
        if (!extension_loaded('pcntl')) {
            die('no support pcntl extension');
        }
        $cron_configs = CronConfig::getAllList(["status" => 0], ["cron_config_id"]);
        if ($cron_configs) {
            pcntl_signal(SIGCHLD, function ($signal) {
                echo "signel $signal received\n";
                while (($pid = pcntl_waitpid(-1, $status, WNOHANG)) > 0) {
                    echo "\t child end pid $pid , status $status\n";
                }
            });
            foreach ($cron_configs as $k => $v) {
                if (self::isCanRun($v['cron_config_id'])) {
                    \Yii::$app->db->close();//关闭链接
                    // 创建子进程
                    $pid = pcntl_fork();
                    if ($pid == -1) {
                        // 返回值为-1，创建失败
                        die('could not fork');
                    } elseif ($pid > 0) {
                        // 返回值大于0，是父进程
                        echo "parent \t", date("H:i:s", time()), "\n";
                        //父进程会得到子进程号，所以这里是父进程执行的逻辑
                        //    pcntl_wait($status, WNOHANG); //等待子进程中断，防止子进程成为僵尸进程。
                    } elseif ($pid == 0) {
                        // 返回值等于0，是子进程
                        self::job($v['cron_config_id']);
                        exit;// 一定要注意退出子进程,否则pcntl_fork() 会被子进程再fork,带来处理上的影响。
                    }

                }
            }
        }
        \Yii::$app->db->close();// solve 主进程 MySQL server has gone
        while (pcntl_waitpid(0, $status) != -1) {
            $status = pcntl_wexitstatus($status);
            echo "Child $status completed\n";
            exit;
        }
        exit;
    }

    /**
     * @param $cron_config_id
     * 待执行的任务
     */
    public static function job($cron_config_id)
    {
        $cron_config = self::getCrontabConfigById($cron_config_id, ["cron_config_id", "name", "status", "start_time", "interval_time", "type", "path", "last_run_time"]);
        if ($cron_config) {
            //类型说明转换
            if ($cron_config['type'] == 0) {
                $type_name = "秒";
            } elseif ($cron_config['type'] == 1) {
                $type_name = "分钟";
            } elseif ($cron_config['type'] == 2) {
                $type_name = "小时";
            } elseif ($cron_config['type'] == 3) {
                $type_name = "日";
            } elseif ($cron_config['type'] == 4) {
                $type_name = "周";
            } elseif ($cron_config['type'] == 5) {
                $type_name = "月";
            } else {
                $type_name = "未知";
            }

            $log = [];
            $log["cron_config_id"] = $cron_config['cron_config_id'];
            try {
                //运行任务
                \Yii::$app->runAction($cron_config['path']);
                //写入日志
                $log["status"] = 0;
                $log["remark"] = "于$cron_config[start_time] 开始按照每间隔$cron_config[interval_time] $type_name 成功执行一次定时$cron_config[name]";
                echo "success";
            } catch (\Exception $exception) {
                echo "异常:" . $exception->getMessage();
                $log["status"] = 1;
                $log["remark"] = "于$cron_config[start_time] 开始按照每间隔$cron_config[interval_time] $type_name 执行一次定时任务$cron_config[name] 出现异常：{$exception->getMessage()}";
            }

            //更新末次执行时间
            $cron_config->last_run_time = date("Y-m-d H:i:s");
            $cron_config->save();
            CronLog::add($log);
        }
    }


    /**
     * @param $cron_config_id
     * @return bool
     * 判断任务是具备执行条件
     */
    public static function isCanRun($cron_config_id)
    {
        try {
            $cron_config = self::getCrontabConfigById($cron_config_id, ["status", "start_time", "path"]);
            //1、判断任务执行路径是否为空
            if (trim($cron_config['path']) == "") {
                return false;
            }
            //2、判断是否已经开启
            if (!($cron_config['status'] == 0)) {
                return false;
            }
            //3、判断是否已经到了开启的时间
            if (!($cron_config['start_time'] <= date("Y-m-d H:i:s"))) {
                return false;
            }
            //4、判断是否已经到了间隔的时间
            if (!self::isTimeArrive($cron_config_id)) {
                return false;
            }
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }


    /**
     * @param $cron_config_id
     * @return bool
     * 判断是否到了间隔时间
     */
    public static function isTimeArrive($cron_config_id)
    {
        try {
            //获取定时配置信息
            $cron_config = self::getCrontabConfigById($cron_config_id, ["cron_config_id", "type", "start_time", "interval_time", "path"]);
            $type = intval($cron_config['type']);
            // 0：间隔时间单位为秒，1：间隔时间单位为分钟，2：间隔时间单位为小时，3：间隔时间单位为天，4：间隔时间单位为周，5：间隔时间单位为月
            $cron_log = $cron_config->getCronLogs()->orderBy(["id" => SORT_DESC])->one();
            if (!$cron_log) {//没有日志记录
                $log_time = $cron_config['start_time'];//开始执行的时间
            } else {
                $log_time = $cron_config['last_run_time'];//末次执行时间，取更新的时间
            }
            $interval_time = intval($cron_config['interval_time']);//间隔时间
            $current_time = date("Y-m-d H:i:s");//当前时间
            $current_time_tamp = strtotime($current_time);//当前时间戳
            $log_time_tamp = strtotime($log_time);//末次执行时间戳

            // 0：间隔时间单位为秒，1：间隔时间单位为分钟，2：间隔时间单位为小时，3：间隔时间单位为天，4：间隔时间单位为周，5：间隔时间单位为月
            //间隔类型判断,  //year（年），month（月），hour（小时）minute（分），second（秒）
            if (in_array($type, [3, 4, 5])) {
                //判断是否到了启动时间
                if (intval(date("H")) == intval(date("H", strtotime($cron_config['start_time'])))) {
                    if ($type == 5) {
                        //月，判断上次执行的月份+间隔的月份是否等于本月本日本时
                        if (date("Ymd") >= date("Ymd", strtotime("+$interval_time month", $log_time_tamp))) {
                            return true;
                        }
                    } elseif ($type == 4) {
                        //周，判断上次执行的周加上间隔的周数是否等于本月本日本时
                        if (date("Ymd") >= date("Ymd", strtotime("+$interval_time week", $log_time_tamp))) {
                            return true;
                        }
                    } elseif ($type == 3) {
                        //日，判断上次执行的日加上间隔的日数是否等于本月本日本时
                        if (date("Ymd") >= date("Ymd", strtotime("+$interval_time day", $log_time_tamp))) {
                            return true;
                        }
                    }
                }

            } else {
                if ($type == 2) {
                    //时，判断上次执行的加上间隔的时数是否等于本月本日本时
                    if (date("YmdH") >= date("YmdH", strtotime("+$interval_time hour", $log_time_tamp))) {
                        return true;
                    } else {
                        return false;
                    }
                } elseif ($type == 1) {
                    $interval = $interval_time * 60;//分钟
                } else {
                    $interval = $interval_time;//秒
                }
                //判断当前时间与最后次执行记录的时间是否间隔所设置的单位时间
                if ($current_time_tamp - $log_time_tamp >= $interval) {
                    return true;
                }
            }
        } catch (\Exception $exception) {
            return false;
        }
        return false;

    }


}

