<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class SignSubscriber implements EventSubscriberInterface
{
    private string $appToken;

    public function __construct(string $appToken)
    {
        $this->appToken = $appToken;
    }

    public function onRequest(RequestEvent $event): void
    {
        $isTest = $event->getRequest()->query->getBoolean('isTest');
        if (!$isTest) {
            return;
        }
        $userVkId = $event->getRequest()->headers->get('X-VK-ID');
        $xSign = $event->getRequest()->headers->get('X-Sign');
        if (empty($xSign) || empty($userVkId)) {
            throw new UnauthorizedHttpException('Wrong user');
        }
        $query_params = [];
        parse_str(parse_url($xSign, PHP_URL_QUERY), $query_params);
        $sign_params = [];
        foreach ($query_params as $name => $value) {
            if (!str_starts_with($name, 'vk_')) {
                continue;
            }
            $sign_params[$name] = $value;
        }
        ksort($sign_params);
        $sign_params_query = http_build_query($sign_params);
        $sign = rtrim(strtr(base64_encode(hash_hmac('sha256', $sign_params_query, $this->appToken, true)), '+/', '-_'), '='); // Получаем хеш-код от строки, используя защищеный ключ приложения. Генерация на основе метода HMAC.

        $status = $sign === $query_params['sign']; // Сравниваем
        if (!$status || !isset($sign_params['vk_user_id']) || $userVkId !== $sign_params['vk_user_id']) {
            throw new UnauthorizedHttpException('Wrong sign');
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onRequest', 4096]
            ],
        ];
    }
}
