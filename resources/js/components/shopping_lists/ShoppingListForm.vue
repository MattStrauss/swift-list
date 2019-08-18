<template>
    <div>
        <div class="card-header" v-if="action === 'Create'">Create New Shopping List
            <span v-if="action !== 'Create'" class="float-right"><a :href="'/shopping-lists/' + list.id">View List </a></span>
        </div>
        <div class="card-header" v-else> {{list.name}}
            <span class="float-right"><a :href="'/shopping-lists/' + list.id">View List </a></span>
        </div>
        <div v-if="success" class="alert alert-primary fade show" role="alert" style="margin:2%;">
            <strong>Saved!</strong> Your changes have been saved.
            <button type="button" class="close" @click="success = false" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
            <div class="card-body">
                <form  @submit.prevent="submit" autocomplete="off">
                    <div class="form-group">
                        <label for="name">List Name</label>
                        <input type="text" class="form-control" name="name" id="name" v-model="list.name">
                        <div v-if="errors && errors.name" class="text-danger">{{ errors.name[0] }}</div>
                    </div>

                        <recipes-section :available-recipes="this.availableRecipes" :initial-included-recipes="this.includedRecipes"></recipes-section>

                    <div class="clearfix"></div> <br>

                        <items-section :available-items="this.availableItems" :initial-included-items="this.includedItems" :aisles="this.aisles"></items-section>

                    <div class="clearfix"></div>
                    <hr>

                    <input type="hidden" name="item_id" value="" v-model="list.id">

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
        props: ['initialAction', 'previousUrl', 'initialList', 'aisles', 'availableRecipes', 'initialIncludedRecipes', 'availableItems', 'initialIncludedItems'],
        data() {
            return {
                includedRecipes: this.initialIncludedRecipes,
                includedItems: this.initialIncludedItems,
                list: this.initialList,
                action: this.initialAction,
                processing: false,
                autoSaved: false,
                success: false,
                errors: {},
            }
        },
        methods:
            {
                submit() {
                    this.processing = true;
                    this.errors = {};
                    this.list['recipes'] = this.includedRecipes;
                    this.list['items'] = this.includedItems;
                    let uri = "/shopping-lists/";
                    let method = "post";
                    if (this.action === 'Update') {
                        uri = '/shopping-lists/' + this.list.id;
                        method = "put";
                    }
                    axios({
                        method: method,
                        url: uri,
                        data: this.list
                    }).then(response => {
                        this.list = response.data;
                        this.processing = false;
                        this.action = "Update";
                        this.success = true;
                        setTimeout(() => {this.success = false;}, 2000);
                    }).catch(error => {
                        if (error.response.status === 422) {
                            this.processing = false;
                            this.errors = error.response.data.errors || {};
                        }
                    });
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
