<?php

use Baiy\ThinkAsync\Subscribe\ConfigFileEventGetter;
use Baiy\ThinkAsync\Subscribe\DemoSubscriber;

return [
    // 异步执行默认队列
    'async_exec_method_queue'        => 'async_exec_method',
    // 异步执行自定义队列
    'async_exec_method_custom_queue' => [
        'async_exec_method_custom' => '自定义异步执行队列',
    ],

    // 异步订阅默认队列名称
    'subscribe_default_queue'        => 'subscribe_default',
    // 异步订阅事件获取类
    'subscribe_event_get_class'      => ConfigFileEventGetter::class,
    // 异步订阅事件配置(可通过修改`subscribe_event_get_class`改变配置来源)
    'subscribe_event_config'         => [
        [
            'name'       => 'donate_succeed',
            'title'      => '捐款成功',
            'queue'      => 'async_send_email', // 事件处理队列 为空使用异步订阅默认队列
            // 事件订阅者配置
            'subscriber' => [
                [\app\event\Email::class, 'donateSucceed'],
            ]
        ],
        [
            'name'       => 'send_email_verify_code',
            'title'      => '发送邮件验证码',
            'queue'      => 'async_send_email', // 事件处理队列 为空使用异步订阅默认队列
            // 事件订阅者配置
            'subscriber' => [
                [\app\event\Email::class, 'verifyCode'],
            ]
        ]
    ]
];