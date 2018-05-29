# yii-crontab-manager
通过这个包，您可以更好的管理您繁杂的定时任务，只需定义一个执行入口即可，通过操作数据库数据实现定时、开启、关闭、设定启动时间等等更灵活的管理

说明：该任务管理器实现是利用多进程的灵活性，在任务开启后，每个任务占用一个子进程，完成后自动释放

准备：安装扩展

要实现PHP的多进程，我们需要两个扩展pcntl和posix，在编译时可以加入：–enable-pcntl，不要使用–disable-pcntl，posix是默认安装的。
php多进程模块依赖pcntl扩展

开始使用：

1、安装
 senman/yii2-crontab-manager dev-master
 
 
2、执行数据库导入
 将vender/yii2-crontab-manager/目录下的  cron-manager.sql导入到数据库
 
3、添加任务

 name:任务名称
 
 remark：备注
 
 status:任务状态，0：正常运行，1：终止运行
 
 start_time:开始运行时间
 
 interval_time:间隔时间，具体单位取决于type的类型
 
 type:字段含义  0：间隔时间单位为秒，1：间隔时间单位为分钟，2：间隔时间单位为小时，3：间隔时间单位为天，4：间隔时间单位为周，5：间隔时间单位为月
 
 path：执行路径
 
 module：所属模块
 
 sort：任务执行的优先顺序，数字越大，越先执行
 
 create_time：创建时间
 
将任务添加到数据库

   $cron_config_model=new CronConfig();
   
   $cron_config_model->name="短信批量发";
   
   $cron_config_model->remark="这是短信批量发";
   
   $cron_config_model->status="0";
   
   $cron_config_model->start_time="2018-05-07 00:15:00";
   
   $cron_config_model->interval_time="1";
   
   $cron_config_model->type="1";
   
   $cron_config_model->path="email/send";
   
   $cron_config_model->module="email";
   
   $cron_config_model->sort="100";
   
   $cron_config_model->create_time="2018-05-28 11:53:19";
   
   $cron_config_model->save();
 
3、编写脚本入口

在项目根目录的console\controllers目录创建CrontabController.php文件，如下：

namespace console\controllers;

use Crontab\models\CronConfig;

use yii\console\Controller;

class CrontabController extends Controller{

    public function actionCrontab(){
    
        CronConfig::run();
        
        }
   } 
 
 
4、执行任务

   执行命令：php yii crontab/crontab
   即可开始按照预设条件进行任务处理
   
5、建议将该命令加入到linux的定时任务任务中，根据需要设定运行间隔时间，例如1分钟跑一次

6、如有任何疑问欢迎加入QQ群：338461207 进行交流


      
   
   
    
