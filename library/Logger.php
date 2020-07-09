<?php

use Monolog\Logger as MonoLogger;

/**
 * Class Logger
 * 日志相关的操作
 *
 * @author 刘文岳
 * @date 2020-06-16 16:59
 *
 * @method static void error(string $message, array $context = [])
 * @method static void debug(string $message, array $context = [])
 * @method static void info(string $message, array $context = [])
 * @method static void warning(string $message, array $context = [])
 * @method static void alert(string $message, array $context = [])
 * @method static void critical(string $message, array $context = [])
 * @method static void emergency(string $message, array $context = [])
 * @method static void log(string $message, array $context = [])
 * @method static void notice(string $message, array $context = [])
 *
 */
final class Logger
{
    /**
     * @var array[Logger]
     */
    private static $logChannel;

    /**
     * @var MonoLogger
     */
    private $monoLogger;

    /**
     * Logger constructor.
     * @param string $name
     */
    private function __construct(string $name)
    {
        $config = PA::$config->get('logger')->toArray();
        if (key_exists($name, $config))
            $config = $config[$name];
        else
            $config = $config['System'];
        if ($config['type'] === 'db') {
            $this->monoLogger = new MonoLogger($name);
            $this->monoLogger->pushHandler(new MysqlLoggerHandler($config['connection']));
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([self::channel(PA::$dispatch->getModuleName()), $name], $arguments);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->monoLogger, $name], $arguments);
    }

    /**
     * 获取对应日志频道的记录对象
     *
     * @param string $channelName 频道名称
     * @return MonoLogger
     */
    public static function channel(string $channelName = 'System'): MonoLogger
    {
        empty(self::$logChannel) && self::$logChannel = [];
        key_exists($channelName, self::$logChannel) || self::$logChannel[$channelName] = new self($channelName);
        return self::$logChannel[$channelName]->monoLogger;
    }
}
