<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasErrorCollection
{
    public array $error = [];

    public function addError(Model $model, $message){
        $this->error[] = [$model->toArray(), 'error' => $message];
    }
}
