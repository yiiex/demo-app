<?php

namespace App\Http\Attributes;

use App\Http\Exceptions\{NotFoundException};
use App\Http\Helpers\RequestHelper;
use Yiisoft\Hydrator\Attribute\Parameter\{ParameterAttributeInterface, ParameterAttributeResolverInterface};
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\Result;
use Yiisoft\Router\CurrentRoute;

final readonly class ModelContextResolver implements ParameterAttributeResolverInterface
{
    public function __construct(private CurrentRoute  $currentRoute)
    {
    }

    public function getParameterValue(
        ParameterAttributeInterface      $attribute,
        ParameterAttributeResolveContext $context,
    ): Result
    {

        if (!$attribute instanceof ModelContext) {
            throw new UnexpectedAttributeException(ModelContext::class, $attribute);
        }

        $arguments = $this->currentRoute->getArguments();
        $class = $attribute->class;
        if ($attribute->routeKey && array_key_exists($attribute->routeKey, $arguments)) {
            $model = $class::model()->findByPk($arguments[$attribute->routeKey], ['with' => $attribute->with]);
            if (!$model) {
                throw new NotFoundException("{$class} not found");
            }
        } else {
            $model = new $class();
        }
        if ($attribute->scenario) {
            $model->setScenario($attribute->scenario);
        }
        return Result::success($model);
    }
}
