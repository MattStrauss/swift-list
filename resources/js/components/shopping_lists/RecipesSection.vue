<template>
    <div>
        <h6>Recipes</h6>
        <auto-complete :items="availableRecipes" :isAsync="false" :model="'recipe'" :placeHolder="'Search Recipes...'" @recipe-added="addRecipe"></auto-complete>

        <h6> Recipes On List <small v-if="includedRecipes.length !== 0">({{includedRecipes.length}})</small></h6>
        <ul v-if="includedRecipes.length !== 0" class="items recipes-on-list">
            <li v-for="(recipe, index) in includedRecipes" :index="recipe.id" class="item-delete-able">
                <i class="fa fa-times fa-hover-show fa-fw" @click="deleteRecipe(index)"></i> <i class="fa fa-times-circle fa-hover-hidden fa-fw"></i> {{ recipe.name }}</li>
        </ul>
        <p v-else class="text-muted"> No recipes on list yet...</p>
    </div>
</template>

<script>
    export default {
        props: ['availableRecipes', 'initialIncludedRecipes'],
        data() {
            return {
                includedRecipes: this.initialIncludedRecipes,
            }
        },
        methods:
            {
                addRecipe(recipe) {
                    this.includedRecipes.push(recipe);
                    this.saveList();
                },
                deleteRecipe(index) {
                    this.$delete(this.includedRecipes, index);
                    this.saveList();
                },
                saveList() {
                    this.$emit('save-list')
                }
            },
    }
</script>
