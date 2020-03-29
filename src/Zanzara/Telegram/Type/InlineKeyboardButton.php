<?php

declare(strict_types=1);

namespace Zanzara\Telegram\Type;

/**
 *
 */
class InlineKeyboardButton
{

    /**
     * @var string
     */
    private $text;

    /**
     * @var string|null
     */
    private $url;

    /**
     * @var LoginUrl|null
     */
    private $loginUrl;

    /**
     * @var string|null
     */
    private $callbackData;

    /**
     * @var string|null
     */
    private $switchInlineQuery;

    /**
     * @var string|null
     */
    private $switchInlineQueryCurrentChat;

    /**
     * @var CallbackGame|null
     */
    private $callbackGame;

    /**
     * @var bool|null
     */
    private $pay;

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return LoginUrl|null
     */
    public function getLoginUrl(): ?LoginUrl
    {
        return $this->loginUrl;
    }

    /**
     * @param LoginUrl|null $loginUrl
     */
    public function setLoginUrl(?LoginUrl $loginUrl): void
    {
        $this->loginUrl = $loginUrl;
    }

    /**
     * @return string|null
     */
    public function getCallbackData(): ?string
    {
        return $this->callbackData;
    }

    /**
     * @param string|null $callbackData
     */
    public function setCallbackData(?string $callbackData): void
    {
        $this->callbackData = $callbackData;
    }

    /**
     * @return string|null
     */
    public function getSwitchInlineQuery(): ?string
    {
        return $this->switchInlineQuery;
    }

    /**
     * @param string|null $switchInlineQuery
     */
    public function setSwitchInlineQuery(?string $switchInlineQuery): void
    {
        $this->switchInlineQuery = $switchInlineQuery;
    }

    /**
     * @return string|null
     */
    public function getSwitchInlineQueryCurrentChat(): ?string
    {
        return $this->switchInlineQueryCurrentChat;
    }

    /**
     * @param string|null $switchInlineQueryCurrentChat
     */
    public function setSwitchInlineQueryCurrentChat(?string $switchInlineQueryCurrentChat): void
    {
        $this->switchInlineQueryCurrentChat = $switchInlineQueryCurrentChat;
    }

    /**
     * @return CallbackGame|null
     */
    public function getCallbackGame(): ?CallbackGame
    {
        return $this->callbackGame;
    }

    /**
     * @param CallbackGame|null $callbackGame
     */
    public function setCallbackGame(?CallbackGame $callbackGame): void
    {
        $this->callbackGame = $callbackGame;
    }

    /**
     * @return bool|null
     */
    public function getPay(): ?bool
    {
        return $this->pay;
    }

    /**
     * @param bool|null $pay
     */
    public function setPay(?bool $pay): void
    {
        $this->pay = $pay;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

}
