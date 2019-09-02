<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Aisle extends Model
{
    /**
     * An Aisle has many Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany('App\Item');
    }

    public static function withCustomOrder($user)
    {
        $aisles = Aisle::all();

        if ($aisle_order = $user->aisle_order) {

            $aisles = $aisles->map(function($aisle) use ($aisle_order) {
                $aisle->order = array_search($aisle->id, $aisle_order);
                return $aisle;

            })->sortBy('order')->values();
        }

        return $aisles;
    }
}
