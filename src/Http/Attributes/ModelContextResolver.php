<?php

namespace App\Http\Attributes;

use App\Http\Exceptions\{ForbiddenException, NotFoundException};
use Yiisoft\Hydrator\Attribute\Parameter\{ParameterAttributeInterface, ParameterAttributeResolverInterface};
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\Result;
use Yiisoft\Injector\Injector;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\User\CurrentUser;

final readonly class ModelContextResolver implements ParameterAttributeResolverInterface
{
    public function __construct(
        private CurrentRoute $currentRoute,
        private Injector     $injector,
        private CurrentUser  $user,
    )
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
            if ($attribute->policy && ($policy = $this->injector->make($attribute->policy))
                && method_exists($policy, $attribute->scenario)
                && !$policy->{$attribute->scenario}($model, $this->user)) {
                throw new ForbiddenException('Access denied');
            }
        }
        return Result::success($model);
    }
}
