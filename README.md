# yii-crontab-manager

Yii-crontab-manager

With this package, you can better manage your multifarious timing tasks, just define an execution entry that can be implemented more flexibly by manipulating database data for timing, opening, closing, setting up time, and so on.



Description: the task manager implements the flexibility of multiple processes. After the task is opened, each task takes up a sub process and automatically releases after completion.



Preparation: installation extension



To implement the multiple processes of PHP, we need two extensions pcntl and POSIX, which can be added at compile time: - enable-pcntl, do not use - disable-pcntl, POSIX is the default installation. PHP multi process modules rely on pcntl extensions



Start using:


通过这个包，您可以更好的管理您繁杂的定时任务，只需定义一个执行入口即可，通过操作数据库数据实现定时、开启、关闭、设定启动时间等等更灵活的管理


说明：该任务管理器实现是利用多进程的灵活性，在任务开启后，每个任务占用一个子进程，完成后自动释放

准备：安装扩展

要实现PHP的多进程，我们需要两个扩展pcntl和posix，在编译时可以加入：–enable-pcntl，不要使用–disable-pcntl，posix是默认安装的。
php多进程模块依赖pcntl扩展

开始使用：



 
1、安装 Install

 composer require senman/yii2-crontab-manager dev-master
 
 
 
2、执行数据库导入


将vender/yii2-crontab-manager/目录下的  cron-manager.sql导入到数据库
 
 
 
Execute the database import
Import cron-manager.sql from vender/yii2-crontab-manager/ directory into database
 
 
 
 
3、添加任务 Add tasks



 name:   任务名称 Task name
 
 remark：备注
 
 status:任务状态，0：正常运行，1：终止运行  Task state, 0: normal operation, 1: terminate operation
 
 start_time:开始运行时间 Start running time
 
 interval_time:间隔时间，具体单位取决于type的类型 Interval time, the specific unit depends on the type of type
 
 type:字段含义  0：间隔时间单位为秒，1：间隔时间单位为分钟，2：间隔时间单位为小时，3：间隔时间单位为天，4：间隔时间单位为周，5：间隔时间单位为月
 
 0: the interval time unit is second, 1: the interval time unit is minute, 2: the interval time unit is hour, 3: the interval time unit is day, 4: the interval time unit is week, 5: the interval time unit is the month.
 
 path：执行路径  Execution path
 
 module：所属模块 Subordinate module
 
 sort：任务执行的优先顺序，数字越大，越先执行 The priority of task execution, the greater the number, the more advanced.
 
 create_time：创建时间  Creation time
 
将任务添加到数据库 Add the task to the database

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
 
3、编写脚本入口 Write the entry of the script

在项目根目录的console\controllers目录创建CrontabController.php文件，如下：
Create the CrontabController.php file in the console\controllers directory of the project root directory, as follows:

namespace console\controllers;

use Crontab\models\CronConfig;

use yii\console\Controller;

class CrontabController extends Controller{

    public function actionCrontab(){
    
        CronConfig::run();
        
        }
   } 
 
 
4、执行任务 Carry out the task

   执行命令：php yii crontab/crontab
   即可开始按照预设条件进行任务处理
   
   Execute the command: PHP Yii crontab/crontab

You can start handling tasks according to preset conditions
   
5、建议将该命令加入到linux的定时任务任务中，根据需要设定运行间隔时间，例如1分钟跑一次
it is recommended that the command be added to the Linux timed task, and set the running interval according to the requirement, such as running once in 1 minutes.


6、若中途想修改执行的时间则需要执行下列两个步骤：

1）修改该任务的cron_config表里的start_time；
2）若是已经有执行记录，则修改该任务的最后一次执行记录cron_log表的update_time更新时间

6、如有任何疑问欢迎加入QQ群：338461207 进行交流
if you have any questions, welcome to join QQ group: 338461207





    
   
   
   
    
