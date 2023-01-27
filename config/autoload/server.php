<?php

declare(strict_types=1);

use Hyperf\Server\Event;
use Hyperf\HttpServer\Server as HttpServer;
use Hyperf\Server\ServerInterface;
use Hyperf\Framework\Bootstrap\WorkerStartCallback;
use Hyperf\Framework\Bootstrap\PipeMessageCallback;
use Hyperf\Framework\Bootstrap\TaskCallback;
use Hyperf\Framework\Bootstrap\WorkerExitCallback;
use Hyperf\Framework\Bootstrap\FinishCallback;

// cpu 数量
$numCPU = swoole_cpu_num();

return [
    'mode' => SWOOLE_PROCESS,
    'servers' => [
        [
            'name' => 'http',
            'type' => ServerInterface::SERVER_HTTP,
            'host' => '0.0.0.0',
            'port' => (int)env('APP_PORT', 9005),
            'sock_type' => SWOOLE_SOCK_TCP,
            'callbacks' => [
                Event::ON_REQUEST => [HttpServer::class, 'onRequest'],
            ],
        ],
    ],
    'settings' => [
        'enable_coroutine' => true,
        'worker_num' => $numCPU / 2,
        'pid_file' => BASE_PATH . '/runtime/hyperf.pid',
        'open_tcp_nodelay' => true,
        'max_coroutine' => 10240,
        'open_http2_protocol' => true,
        'max_request' => 10240,
        'socket_buffer_size' => 2 * 1024 * 1024,
        'buffer_output_size' => 2 * 1024 * 1024,
        'task_enable_coroutine' => false,
        'task_worker_num' => env('TASK_WORKER_NUM') ?? ($numCPU > 4 ? $numCPU / 2 : $numCPU),
    ],
    'callbacks' => [
        Event::ON_WORKER_START => [WorkerStartCallback::class, 'onWorkerStart'],
        Event::ON_PIPE_MESSAGE => [PipeMessageCallback::class, 'onPipeMessage'],
        Event::ON_WORKER_EXIT => [WorkerExitCallback::class, 'onWorkerExit'],
        Event::ON_TASK => [TaskCallback::class, 'onTask'],
        Event::ON_FINISH => [FinishCallback::class, 'onFinish']
    ],
];
