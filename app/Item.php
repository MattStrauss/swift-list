<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'user_id', 'aisle_id', 'favorite'
    ];


    /**
     * An Item has many Recipes
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function recipes()
    {
        return $this->belongsToMany('App\Recipe')->withTimestamps();
    }

    /**
     * An Item belongs to one Aisle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function aisle()
    {
        return $this->belongsTo('App\Aisle');
    }


    /**
     * An Item belongs to one User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * An Item has many ShoppingLists
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shoppingLists()
    {
        return $this->belongsToMany('App\ShoppingList', 'item_shopping_list', 'item_id', 'shopping_list_id')->withTimestamps();
    }
}
