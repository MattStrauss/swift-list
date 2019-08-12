<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'instructions', 'user_id', 'category_id'
    ];


    /**
     * A Recipe belongs to one User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }


    /**
     * A Recipe belongs to one Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }


    /**
     * A Recipe has many Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function items()
    {
        return $this->belongsToMany('App\Item')->withTimestamps();
    }


    /**
     * A Recipe has many ShoppingLists
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shoppingLists()
    {
        return $this->belongsToMany('App\ShoppingList', 'recipe_shopping_list', 'recipe_id ', 'shopping_list_id')->withTimestamps();

    }
}
