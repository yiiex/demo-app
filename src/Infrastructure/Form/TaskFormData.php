<?php

namespace App\Infrastructure\Form;

use App\Models\Task;
use App\Models\TaskUser;
use App\Shared\Caster\Casts\DateTimeCast;
use Yii1x\ActiveRecord\Model\Event;

/**
 * @property Task $model
 */
class TaskFormData extends FormData
{
    protected array $casts = [
        'deadline' => DateTimeCast::class,
    ];

    public function setAttributes(array $attributes): static
    {
        if (isset($attributes['rawUsers']) && is_array($attributes['rawUsers'])) {
            $newUsers = array_diff($attributes['rawUsers'], array_column($this->model->user_links, 'user_id'));
            foreach ($newUsers as $newUser) {
                $user = new TaskUser();
                $user->setAttributes(['user_id' => $newUser]);
                $this->model->addRelatedRecord('user_links', $user, true);
            }
        }
        $this->model->onAfterSave = function (Event $event) {
            foreach ($event->sender->user_links as $user) {
                if (!in_array($user->user_id, $event->sender->rawUsers ?: [])) {
                    $user->delete();
                } elseif ($user->isNewRecord) {
                    $user->task_id = $event->sender->id;
                    $user->save(false);
                }
            }
        };
        return parent::setAttributes($attributes);
    }

    public function getAttributes(): array
    {
        return array_merge(parent::getAttributes(), [
            'rawUsers' => $this->model->user_links ? array_column($this->model->user_links, 'user_id') : null
        ]);
    }
}
