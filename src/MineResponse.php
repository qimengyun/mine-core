<?php

declare(strict_types=1);

namespace Mine;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Response;
use Mine\Log\RequestIdHolder;
use Psr\Http\Message\ResponseInterface;
use Swow\Psr7\Message\ResponsePlusInterface;

/**
 * Class MineResponse.
 */
class MineResponse extends Response
{
    public function success(?string $message = '', array|object $data = [], int $code = 200): ResponseInterface
    {
        $format = [
            'requestId' => RequestIdHolder::getId(),
            'success' => true,
            'message' => $message,
            'code' => $code,
            'data' => &$data,
        ];
        $format = $this->toJson($format);
        return $this->handleHeader($this->getResponse())
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($format));
    }

    public function error(string $message = '', int $code = 500, array $data = []): ResponseInterface
    {
        $format = [
            'requestId' => RequestIdHolder::getId(),
            'success' => false,
            'code' => $code,
            'message' => $message,
        ];

        if (!empty($data)) {
            $format['data'] = &$data;
        }

        $format = $this->toJson($format);
        return $this->handleHeader($this->getResponse())
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($format));
    }

    /**
     * 向浏览器输出图片.
     */
    public function responseImage(string $image, string $type = 'image/png'): ResponseInterface
    {
        return $this->handleHeader($this->getResponse())
            ->withAddedHeader('content-type', $type)
            ->withBody(new SwooleStream($image));
    }

    public function getResponse(): ResponsePlusInterface
    {
        return parent::getResponse(); // TODO: Change the autogenerated stub
    }

    private function handleHeader(ResponseInterface $response): ResponseInterface
    {
        $headers = [
            'Server' => 'QiMYun',
        ];
        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }
        return $response;
    }
}