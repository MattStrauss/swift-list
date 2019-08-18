<template>
    <div>
        <div class="card-header" v-if="action === 'Create'">Create New Shopping List</div>
        <div class="card-header" v-else> {{listName}} </div>
            <div class="card-body">
                <form  @submit.prevent="submit" autocomplete="off">
                    <div class="form-group">
                        <label for="name">List Name</label>
                        <input type="text" class="form-control" name="name" id="name" v-model="listName">
                    </div>

                        <recipes-section :available-recipes="this.availableRecipes" :initial-included-recipes="this.includedRecipes"></recipes-section>

                    <div class="clearfix"></div> <br>

                        <items-section :available-items="this.availableItems" :initial-included-items="this.includedItems" :aisles="this.aisles"></items-section>

                    <div class="clearfix"></div>
                    <hr>

                    <div class="form-buttons">
                        <a :href="previousUrl" class="btn btn-outline-secondary float-left">Back</a>

                        <button type="submit" :disabled="processing" class="btn btn-outline-primary float-right">
                            <span v-show="processing" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            {{action}}
                        </button>
                    </div>
                </form>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['action', 'previousUrl', 'initialListName', 'aisles', 'availableRecipes', 'initialIncludedRecipes', 'availableItems', 'initialIncludedItems'],
        data() {
            return {
                includedRecipes: this.initialIncludedRecipes,
                includedItems: this.initialIncludedItems,
                listName: this.initialListName,
                processing: false,
            }
        },
        methods:
            {
                submit() {
                  //
                },
            },
    }
</script>

<style>
    hr {
        margin-top: 15px;
    }

    .form-buttons {
        justify-content: space-between;
        padding: 10px 5px 30px 5px;
        margin:2%;
    }
</style>
