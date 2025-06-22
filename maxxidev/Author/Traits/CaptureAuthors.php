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

    public function touch()
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
        $model = config('auth.providers.users.model');
        
        // Ensure a valid class name is returned
        if (empty($model) || !is_string($model) || !class_exists($model)) {
            return null;
        }
        
        return $model;
    }

    public function createdBy()
    {
        $userModel = $this->getAuthUserModelClass();
        
        // If user model class doesn't exist or is invalid, return null instead of calling belongsTo
        if (!$userModel) {
            return null;
        }
        
        return $this->belongsTo($userModel, $this->getCreatedByColumn());
    }

    public function updatedBy()
    {
        $userModel = $this->getAuthUserModelClass();
        
        // If user model class doesn't exist or is invalid, return null instead of calling belongsTo
        if (!$userModel) {
            return null;
        }
        
        return $this->belongsTo($userModel, $this->getUpdatedByColumn());
    }

    public function deletedBy()
    {
        $userModel = $this->getAuthUserModelClass();
        
        // If user model class doesn't exist or is invalid, return null instead of calling belongsTo
        if (!$userModel) {
            return null;
        }
        
        return $this->belongsTo($userModel, $this->getDeletedByColumn());
    }
}
