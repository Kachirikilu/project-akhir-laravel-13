<?php

namespace App\Livewire\Global;

trait HasErrorCount
{
    public function getErrorCount(array $fields)
    {
        $errors = $this->getErrorBag()->toArray();
        $count = 0;

        foreach ($fields as $field) {
            foreach ($errors as $key => $messages) {
                if (str_starts_with($key, $field)) {
                    $count += count($messages);
                }
            }
        }

        return $count;
    }
}

    
