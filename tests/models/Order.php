<?php

namespace Tacone\Coffee\Test;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function customer()
    {
        return $this->hasOne(CustomerDetail::class);
    }
}
