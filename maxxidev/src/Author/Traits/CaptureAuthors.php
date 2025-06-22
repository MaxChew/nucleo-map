<?php

namespace Maxxidev\Author\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;

trait CaptureAuthors
{
    protected static function bootCaptureAuthors()
    {
        static::saving(function ($model) {
            $model->updateAuthors();
        });

        static::deleted(function ($model) {
            if ($model->exists) {
                $userId = optional(auth()->user())->getAuthIdentifier();
                $model->{$model->getDeletedByColumn()} = $userId;
                $model->save();
            }
        });

        if (method_exists(static::class, 'restoring')) {
            static::restoring(function ($model) {
                $model->{$model->getDeletedByColumn()} = null;
            });
        }
    }

    protected function updateAuthors()
    {
        $userId = optional(auth()->user())->getAuthIdentifier();

        if (! $this->isDirty($this->getUpdatedByColumn())) {
            $this->setUpdatedBy($userId);
        }

        if (! $this->exists && ! $this->isDirty($this->getCreatedByColumn())) {
            $this->setCreatedBy($userId);
        }
    }

    public function touch($attributes = null)
    {
        if (! $this->usesTimestamps()) {
            return false;
        }

        $this->updateTimestamps();
        $this->updateAuthors();

        return $this->save();
    }

    public function setCreatedBy($value)
    {
        $this->{$this->getCreatedByColumn()} = $value;

        return $this;
    }

    public function setUpdatedBy($value)
    {
        $this->{$this->getUpdatedByColumn()} = $value;

        return $this;
    }

    public function getCreatedByColumn()
    {
        return defined('static::CREATED_BY') ? static::CREATED_BY : 'created_by';
    }

    public function getUpdatedByColumn()
    {
        return defined('static::UPDATED_BY') ? static::UPDATED_BY : 'updated_by';
    }

    public function getDeletedByColumn()
    {
        return defined('static::DELETED_BY') ? static::DELETED_BY : 'deleted_by';
    }

    private function isSoftDeleting()
    {
        if (isset(class_uses($this)[SoftDeletes::class])) {
            return ! $this->isForceDeleting();
        }

        return false;
    }

    private function getAuthUserModelClass()
    {
        return config('auth.providers.users.model');
    }

    public function createdBy()
    {
        $user_model = $this->getAuthUserModelClass();
        if ($user_model && class_exists($user_model)) {
            return $this->belongsTo($user_model, 'created_by');
        }
        return null;
    }

    public function updatedBy()
    {
        $user_model = $this->getAuthUserModelClass();
        if ($user_model && class_exists($user_model)) {
            return $this->belongsTo($user_model, 'updated_by');
        }
        return null;
    }

    public function deletedBy()
    {
        $user_model = $this->getAuthUserModelClass();
        if ($user_model && class_exists($user_model)) {
            return $this->belongsTo($user_model, 'deleted_by');
        }
        return null;
    }
}
