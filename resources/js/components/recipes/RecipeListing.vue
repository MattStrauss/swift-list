<template>
    <div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="search"><i class="fas fa-search fa-fw"></i></span>
            </div>
            <input v-model="search" type="text" class="form-control" placeholder="Search Recipes..." aria-label="Search Recipes" aria-describedby="search">
        </div>
        <div class="list-group">
            <div v-if="Object.keys(filteredRecipes).length > 0" v-for="(recipe, index) in filteredRecipes" :key="recipe.id">
                <div v-if=" index === 0 || (index > 0 && recipe.category.id !== filteredRecipes[index - 1].category.id)"
                     class="list-group-item d-flex justify-content-between align-items-center active">
                    <h4 class="mb-1">{{ recipe.category.name }}</h4>
                    <span class="badge badge-light badge-pill">{{categoryCount(recipe.category.id)}}</span>
                </div>
                <a :href="'/recipes/' + recipe.id" class="list-group-item list-group-item-action">{{recipe.name}}</a>
            </div>

            <h4 v-if="Object.keys(filteredRecipes).length === 0"> No recipes found! </h4>

        </div>
    </div>
</template>


<script>
    export default {
        props: ['recipes'],
        data() {
            return {
                search: "",
            }
        },
        methods:
            {
                categoryCount(categoryID) {
                    let count = 0;
                    this.filteredRecipes.filter(function (recipe) {
                        if (recipe.category.id === categoryID) count++;
                    });
                    return count;
                },
            },
        computed:
            {
                filteredRecipes() {
                    let self = this; // for scope below
                    return this.recipes.filter(function (recipe) {
                        return recipe.name.toLowerCase().indexOf(self.search.toLowerCase()) >= 0
                    });
                },
            }
    }
</script>
