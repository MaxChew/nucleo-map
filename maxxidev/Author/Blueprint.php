<?php

namespace Maxxidev\Author;

class Blueprint
{
    public function authors()
    {
        return function () {
            $this->integer('created_by')->unsigned()->nullable();
            $this->integer('updated_by')->unsigned()->nullable();
        };
    }

    public function dropAuthors()
    {
        return function () {
            $this->dropColumn('created_by', 'updated_by');
        };
    }

    public function softDeletesWithAuthor()
    {
        return function () {
            $this->softDeletes();
            $this->integer('deleted_by')->unsigned()->nullable();
        };
    }

    public function dropSoftDeletesWithAuthor()
    {
        return function () {
            $this->dropSoftDeletes();
            $this->dropColumn('deleted_by');
        };
    }
}
