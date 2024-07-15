<?php

declare(strict_types=1);

namespace Mine;

use Mine\Traits\ControllerTrait;

/**
 * 后台控制器基类
 * Class MineController.
 */
abstract class MineController
{
    use ControllerTrait;

    public function __construct(
        readonly protected MineRequest  $request,
        readonly protected MineResponse $response,
        readonly private MineValidation $validationFactory
    )
    {
    }

    public function getResponse(): MineResponse
    {
        return $this->response;
    }

    public function getRequest(): MineRequest
    {
        return $this->request;
    }

    public function validation($validation, $data): bool
    {
        return $this->validationFactory->validation($validation, $data);
    }

    public function getValidationError(): string
    {
        return $this->validationFactory->getError();
    }
}
