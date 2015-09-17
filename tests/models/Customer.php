<?php

namespace Tacone\Coffee\Test;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public function getFullNameAttribute()
    {
        return $this->name.' '.$this->surname;
    }
    public function details()
    {
        return $this->hasOne(CustomerDetail::class);
    }
}
