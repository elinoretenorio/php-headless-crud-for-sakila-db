<?php

declare(strict_types=1);

namespace Sakila\Category;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Laminas\Diactoros\Response\JsonResponse;

class CategoryController 
{
    const ERROR_INVALID_INPUT = "Invalid input";

    private ICategoryService $service;

    public function __construct(ICategoryService $service)
    {
        $this->service = $service;        
    }

    public function insert(RequestInterface $request, array $args): ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents(), true);
        if (empty($data)) {
            $data = $request->getParsedBody();
        }

        /** @var CategoryModel $model */
        $model = $this->service->createModel($data);

        $result = $this->service->insert($model);

        return new JsonResponse(["result" => $result]);
    }

    public function update(RequestInterface $request, array $args): ResponseInterface
    {
        $categoryId = (int) ($args["category_id"] ?? 0);
        if ($categoryId <= 0) {
            return new JsonResponse(["result" => $categoryId, "message" => self::ERROR_INVALID_INPUT]);
        }

        $data = json_decode($request->getBody()->getContents(), true);
        if (empty($data)) {
            $data = $request->getParsedBody();
        }

        /** @var CategoryModel $model */
        $model = $this->service->createModel($data);
        $model->setCategoryId($categoryId);

        $result = $this->service->update($model);

        return new JsonResponse(["result" => $result]);
    }

    public function get(RequestInterface $request, array $args): ResponseInterface
    {
        $categoryId = (int) ($args["category_id"] ?? 0);
        if ($categoryId <= 0) {
            return new JsonResponse(["result" => $categoryId, "message" => self::ERROR_INVALID_INPUT]);
        }

        /** @var CategoryModel $model */
        $model = $this->service->get($categoryId);

        return new JsonResponse(["result" => $model->jsonSerialize()]);
    }

    public function getAll(RequestInterface $request, array $args): ResponseInterface
    {
        $models = $this->service->getAll();

        $result = [];

        /** @var CategoryModel $model */
        foreach ($models as $model) {
            $result[] = $model->jsonSerialize();
        }

        return new JsonResponse(["result" => $result]);
    }

    public function delete(RequestInterface $request, array $args): ResponseInterface
    {
        $categoryId = (int) ($args["category_id"] ?? 0);
        if ($categoryId <= 0) {
            return new JsonResponse(["result" => $categoryId, "message" => self::ERROR_INVALID_INPUT]);
        }

        $result = $this->service->delete($categoryId);

        return new JsonResponse(["result" => $result]);
    }
}