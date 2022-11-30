<?php
declare (strict_types = 1);

namespace app\command;

use app\event\XZHMail;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class GetMailLabelsTask extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('getLabelsTask')
            ->setDescription('the task command')
            ->addArgument('action', Argument::OPTIONAL, "action", '')
            ->addArgument('force', Argument::OPTIONAL, "force", '')
            ->addOption('time',null,Option::VALUE_OPTIONAL,'执行间隔(秒)',120);
    }

    protected function execute(Input $input, Output $output)
    {
        //获取输入参数
        $action = trim($input->getArgument('action'));
        $force = trim($input->getArgument('force'));

        $task = new \EasyTask\Task();
        $task->addClass(XZHMail::class,'getLabels','getMailLabels',$input->getOption('time'));
        switch ($action){
            case 'start':{
                $task->start();
                break;
            }
            case 'status':{
                $task->status();
                break;
            }
            case 'stop':{
                $force = ($force == 'force'); //是否强制停止
                $task->stop($force);
                break;
            }
            default:{
                exit('Command is not exist');
            }
        }
    }
}
