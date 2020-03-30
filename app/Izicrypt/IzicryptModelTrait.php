<?php

namespace App\Izicrypt;

use App\Izicrypt\Facade\Izicrypt;

trait IzicryptModelTrait
{
    public function decrypt($encrypted=[], $state='only')
    {
        if(is_bool($state)) {
            $raw = $state;
            $state = 'only';
        }

        Izicrypt::itemCollectionDecrypt($this, $encrypted, $state, $raw);
        
        return $this;
    }
}