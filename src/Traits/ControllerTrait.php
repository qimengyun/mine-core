<?php

declare(strict_types=1);

namespace Mine\Traits;

use Mine\MineRequest;
use Mine\MineResponse;
use Psr\Http\Message\ResponseInterface;

trait ControllerTrait
{
    abstract public function getRequest(): MineRequest;

    abstract public function getResponse(): MineResponse;

    public function success(null|array|object|string $msgOrData = '', array|object $data = [], int $code = 200): ResponseInterface
    {
        if (is_string($msgOrData) || is_null($msgOrData)) {
            return $this->getResponse()->success($msgOrData, $data, $code);
        }
        if (is_array($msgOrData) || is_object($msgOrData)) {
            return $this->getResponse()->success('', $msgOrData, $code);
        }
        return $this->getResponse()->success('', $data, $code);
    }

    public function error(string $message = '', int $code = 500, array $data = []): ResponseInterface
    {
        return $this->getResponse()->error($message, $code, $data);
    }

    /**
     * 跳转.
     */
    public function redirect(string $toUrl, int $status = 302, string $schema = 'http'): ResponseInterface
    {
        return $this->getResponse()->redirect($toUrl, $status, $schema);
    }

    /**
     * 下载文件.
     */
    public function _download(string $filePath, string $name = ''): ResponseInterface
    {
        return $this->getResponse()->download($filePath, $name);
    }
}
