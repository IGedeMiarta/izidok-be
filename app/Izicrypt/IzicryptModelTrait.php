<?php

namespace App\Izicrypt;

use App\Izicrypt\Facade\Izicrypt;

trait IzicryptModelTrait
{
    public function decrypt($encrypted=[], $state='only')
    {
        Izicrypt::itemCollectionDecrypt($this, $encrypted, $state);
        
        return $this;
    }
}