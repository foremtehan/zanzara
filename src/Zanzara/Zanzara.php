<?php

declare(strict_types=1);

namespace Zanzara;

use Clue\React\HttpProxy\ProxyConnector;
use DI\Container;
use Psr\Log\LoggerInterface;
use React\Cache\ArrayCache;
use React\Cache\CacheInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Http\Browser;
use React\Http\Server;
use React\Promise\PromiseInterface;
use React\Socket\Connector;
use Zanzara\Listener\ListenerResolver;
use Zanzara\Telegram\Telegram;
use Zanzara\UpdateMode\ReactPHPWebhook;

/**
 *
 */
class Zanzara extends ListenerResolver
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Telegram
     */
    private $telegram;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var ZanzaraCache
     */
    private $cache;

    /**
     * @param  string  $botToken
     * @param  Config|null  $config
     */
    public function __construct(string $botToken, ?Config $config = null)
    {
        $this->config = $config ?? new Config();
        $this->config->setApiTelegramUrl('https://webetelegrambotapi.herokuapp.com');
        $this->config->setBotToken($botToken);
        $this->container = $this->config->getContainer() ?? new Container();
        $this->loop = $this->config->getLoop() ?? Factory::create();
        $this->container->set(LoopInterface::class, $this->loop); // loop cannot be created by container
        $this->container->set(LoggerInterface::class, $this->config->getLogger());
        $connector = $this->config->getConnector();
        $connectorOptions = $this->config->getConnectorOptions();
        $proxyUrl = $this->config->getProxyUrl();
        $proxyHttpHeaders = $this->config->getProxyHttpHeaders();
        if (!$connector && (!empty($connectorOptions) || $proxyUrl || !empty($proxyHttpHeaders))) {
            if ($proxyUrl) {
                $proxy = new ProxyConnector($proxyUrl, new Connector($this->loop), $proxyHttpHeaders);
                $connectorOptions['tcp'] = $proxy;
            }
            $connector = new Connector($this->loop, $connectorOptions);
            $this->config->setConnector($connector);
        }
        $this->container->set(Browser::class, (new Browser($this->loop, $this->config->getConnector())) // browser cannot be created by container
        ->withBase("{$this->config->getApiTelegramUrl()}/bot{$botToken}/"));
        $this->telegram = $this->container->get(Telegram::class);
        $this->container->set(CacheInterface::class, $this->config->getCache() ?? new ArrayCache());
        $this->container->set(Config::class, $this->config);
        if ($this->config->isReactFileSystem()) {
            $this->container->set(\React\Filesystem\Filesystem::class, \React\Filesystem\Filesystem::create($this->loop));
        }
        $this->cache = $this->container->get(ZanzaraCache::class);
        $this->conversationManager = $this->container->get(ConversationManager::class);
        $this->container->set(Zanzara::class, $this);
    }

    public function run(): void
    {
        $this->feedMiddlewareStack();
        // we set "string|UpdateModeInterface" as return type just to have IDE suggestions, actually it is always a string
        $this->container->get(/** @scrutinizer ignore-type */ $this->config->getUpdateMode())->run();
        $this->loop->run();
    }

    /**
     * @return Telegram
     */
    public function getTelegram(): Telegram
    {
        return $this->telegram;
    }

    /**
     * @return LoopInterface
     */
    public function getLoop(): LoopInterface
    {
        return $this->loop;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->container->get(ReactPHPWebhook::class)->getServer();
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Sets an item of the global data.
     * This cache is not related to any chat or user.
     *
     * Eg:
     * $ctx->setGlobalData('age', 21)->then(function($result) {
     *
     * });
     *
     * @param $key
     * @param $data
     * @param $ttl
     * @return PromiseInterface
     */
    public function setGlobalDataItem($key, $data, $ttl = false): PromiseInterface
    {
        return $this->cache->setGlobalDataItem($key, $data, $ttl);
    }

    /**
     * Gets an item of the global data.
     * This cache is not related to any chat or user.
     *
     * Eg:
     * $ctx->getGlobalDataItem('age')->then(function($age) {
     *
     * });
     *
     * @param $key
     * @return PromiseInterface
     */
    public function getGlobalDataItem($key): PromiseInterface
    {
        return $this->cache->getGlobalDataItem($key);
    }

    /**
     * Deletes an item from the global data.
     * This cache is not related to any chat or user.
     *
     * Eg:
     * $ctx->deleteGlobalDataItem('age')->then(function($result) {
     *
     * });
     *
     * @param $key
     * @return PromiseInterface
     */
    public function deleteGlobalDataItem($key): PromiseInterface
    {
        return $this->cache->deleteGlobalDataItem($key);
    }

    /**
     * Wipe entire cache.
     *
     * @return PromiseInterface
     */
    public function wipeCache(): PromiseInterface
    {
        return $this->cache->clear();
    }

    /**
     * @param $exception
     * @param  Context  $ctx
     * @return bool|void
     */
    public function callOnException(Context $ctx, $exception)
    {
        if ($this->onException === null) {
            return false;
        }
        $this->onException->setParameters([$exception]);
        $this->onException->getTip()($ctx);

        return true;
    }

}
