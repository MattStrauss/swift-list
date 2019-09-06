<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    /**
     * If the user has set a custom aisle order, apply it to the items ordering
     *
     * @param $items
     *
     * @return mixed
     */
    public static function applyCustomAisleOrder($items)
    {
        if ($aisle_order = Auth::user()->aisle_order) {
            $items = $items->sortBy(function ($value, $key) use ($aisle_order) {
                return array_search($key, $aisle_order);
            });
        }

        return $items;
    }

    /**
     * If the user has set a custom aisle order, apply it to the items ordering, customized for Javascript rendering
     *
     * @param $items
     *
     * @return mixed
     */
    public static function applyCustomAisleOrderForJavascriptRendering($items)
    {
        if ($aisle_order = Auth::user()->aisle_order) {
            $items = $items->sortBy(function ($value) use ($aisle_order) {
                return array_search($value->aisle_id, $aisle_order);
            })->values()->all();
        }

        return $items;
    }
}
