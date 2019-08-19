<template>
    <div>
    <div class="card-header">{{recipe.name}}</div>
        <div v-if="success" class="alert alert-primary fade show" role="alert" style="margin:2%;">
            <strong>Recipe Updated!</strong> Your changes have been saved.
            <button type="button" class="close" @click="success = false" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form style="margin:2%;" @submit.prevent="submit" autocomplete="off">
            <div class="form-group">
                <label for="name">Recipe Name</label>
                <input type="text" class="form-control" name="name" id="name" v-model="recipe.name">
                <div v-if="errors && errors.name" class="text-danger">{{ errors.name[0] }}</div>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select class="form-control" name="category" id="category" v-model="recipe.category_id">
                    <option> </option>
                    <option v-for="category in categories" :value="category.id">
                        {{category.name}}
                    </option>
                </select>
                <div v-if="errors && errors.category_id" class="text-danger">{{ errors.category_id[0] }}</div>
            </div>
            <div class="form-group">
                <label for="instructions">Instructions</label>
                <textarea class="form-control" name="instructions" id="instructions" rows="6" v-model="recipe.instructions"></textarea>
                <div v-if="errors && errors.instructions" class="text-danger">{{ errors.instructions[0] }}</div>
            </div>
            <div class="form-group">
                <label>
                    Ingredients <a data-toggle="tooltip" title="Add ingredient" class="cursor-pointer" @click="createOrEditItem()">
                    <i class="fas fa-plus-circle fa-fw text-secondary"></i>
                </a>
                </label>
                <ul class="list-group list-group-flush">
                    <li v-if="items.length < 1" class="list-group-item"> No ingredients... </li>
                    <li v-for="(item, index) in items" :key="item.id" class="list-group-item">
                        <a :id="'item_' + index" data-toggle="tooltip" title="Remove ingredient" class="cursor-pointer" @click="removeItem(index)">
                            <i class="fas fa-minus-circle fa-fw text-danger"></i>
                        </a>
                        <a :id="'item_' + index" data-toggle="tooltip" title="Edit ingredient" class="cursor-pointer" @click="createOrEditItem(item)">
                            <i class="fas fa-pen-square fa-fw text-secondary"></i>
                        </a>
                        {{ item.name }}
                        <small class="text-muted">({{ item.aisle.name }})</small>
                    </li>
                </ul>
            </div>

            <hr>

            <div class="form-buttons">
                <a :href="previousUrl" class="btn btn-outline-secondary float-left">Back</a>

                <button type="submit" @submit="submit('flashSuccess')" :disabled="processing" class="btn btn-outline-primary float-right">
                    <span v-show="processing" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    {{action}}
                </button>
            </div>

        </form>
    </div>
</template>

<script>
    export default {
        props: ['initialRecipe', 'initialItems', 'categories', 'previousUrl', 'action'],
        data() {
            return {
                recipe: this.initialRecipe,
                items: this.initialItems,
                errors: {},
                processing: false,
                success: false,
            }
        },
        created() {
            Event.$on('item-added', this.addItem);
            Event.$on('item-edited', this.editItem);
        },
        beforeDestroy() {
            Event.$off('item-added');
            Event.$off('item-edited');
        },
        methods: {
            submit(flashSuccess) {
                this.processing = true;
                this.recipe['items'] = this.items;
                this.errors = {};
                let uri = "/recipes/";
                let method = "post";
                if (this.action === 'Update') {
                    uri = '/recipes/' + this.recipe.id;
                    method = "put";
                }
                axios({
                    method: method,
                    url: uri,
                    data: this.recipe
                }).then(response => {
                    this.processing = false;
                    if (this.action === 'Add') {
                        window.location = response.data.redirect;
                    } else {
                        this.success = flashSuccess;
                        setTimeout(() => {this.success = false;}, 2000);
                    }
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.processing = false;
                        this.errors = error.response.data.errors || {};
                    }
                });
            },
            updateRecipe() {
                if (this.action === "Update") {
                    this.submit();
                }
            },
            addItem(item) {
                this.items.push(item);
                this.updateRecipe();
            },
            editItem(item) {
                let itemToRemove = this.items.find(function(e) {
                    return e.id === item.id;
                });
                let index = this.items.indexOf(itemToRemove);
                this.items.splice(index, 1, item);
            },
            removeItem(index) {
                $('#item_' + index).tooltip('dispose'); // have to figure out non-jQuery/Vue way to do this...
                this.$delete(this.items, index);
                this.updateRecipe();
            },
            createOrEditItem(item) {
                this.$emit('open-item-modal');
                if (item) {
                    Event.$emit('open-item-modal-edit', item);
                }
            },
        }
    }
</script>

<style>
    .form-buttons {
        justify-content: space-between;
        padding: 10px 5px 60px 5px;
    }

    hr {
        margin-top: -10px;
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>
