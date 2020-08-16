<?php

/**
 * 监控文件或目录的变化，基于inotify扩展
 */

namespace Util;

class Inotify
{
    private $fd;
    private $watchPath;
    private $watchMask; //
    private $watchHandler; // 回调函数
    private $doing = false; // 正在执行watchHandler
    // 确定需要检测的文件类型
    private $fileTypes = [
        '.php' => true
    ];
    private $wdPath = []; // 描述符对应的路径
    private $pathWd = []; // 路径对应的描述符

    public function __construct($watchPath, callable $watchHandler, $watchMask = IN_CREATE | IN_DELETE | IN_MODIFY | IN_MOVE)
    {
        if (!extension_loaded('inotify')) {
            die('请安装inotify扩展');
        }
        $this->fd = inotify_init();
        $this->watchPath = $watchPath;
        $this->watchMask = $watchMask;
        $this->watchHandler = $watchHandler;
        $this->watch();
    }

    public function __destruct()
    {
        $this->stop();
    }

    // 添加需要校验的文件类型
    public function addFileType($type)
    {
        $type = '.' . trim($type, '.');
        $this->fileTypes[$type] = true;
    }

    public function addFileTypes(array $types)
    {
        foreach ($types as $type) {
            $this->addFileType($type);
        }
    }

    public function start()
    {
        swoole_event_add($this->fd, function ($fp) {
            $events = inotify_read($fp);
            if (empty($events)) {
                return null;
            }

            foreach ($events as $event) {
                if ($event['mask'] == IN_IGNORED) {
                    continue;
                }

                $fileType = strchr($event['name'], '.');
                if (!isset($this->fileTypes[$fileType])) {
                    continue;
                }

                if ($this->doing) {
                    continue;
                }
                // 延迟更新，做缓冲
                swoole_timer_after(100, function () use ($event) {
                    dd($event, "执行watchHandler");
                    // 回调自定义函数方法
                    call_user_func_array($this->watchHandler, [$event]);
                    //  标记当前以重启结束
                    $this->doing = false;
                });
                $this->doing = true;
                break;
            }
        });
    }

    public function stop()
    {
        if ($this->fd) {
            swoole_event_del($this->fd);
            fclose($this->fd);
        }
    }

    public function getWatchedFileCount()
    {
        return count($this->wdPath);
    }

    protected function watch()
    {
        $this->_watch($this->watchPath);
    }

    /**
     * 通过递归去获取指定的目录下的文件，然后一一进行监控
     * 对每一个文件进行inotify_add_watch 文件事件注册
     */
    protected function _watch($path)
    {
        $wd = inotify_add_watch($this->fd, $path, $this->watchMask);
        if ($wd === false) {
            return false;
        }
        $this->bind($wd, $path);

        if (is_dir($path)) {
            $wd = inotify_add_watch($this->fd, $path, $this->watchMask);
            if ($wd === false) {
                return false;
            }
            $this->bind($wd, $path);
            // 列出 $path中 目录中的文件和目录：
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $file = $path . DIRECTORY_SEPARATOR . $file;
                if (is_dir($file)) {
                    // 利用递归把子文件也一并的写入
                    $this->_watch($file);
                }
            }
        }
        return true;
    }

    protected function clearWatch()
    {
        foreach ($this->wdPath as $wd => $path) {
            @inotify_rm_watch($this->fd, $wd);
        }
        $this->wdPath = [];
        $this->pathWd = [];
    }

    protected function bind($wd, $path)
    {
        $this->pathWd[$path] = $wd;
        $this->wdPath[$wd] = $path;
    }

    protected function unbind($wd, $path = null)
    {
        unset($this->wdPath[$wd]);
        if ($path !== null) {
            unset($this->pathWd[$path]);
        }
    }

}
