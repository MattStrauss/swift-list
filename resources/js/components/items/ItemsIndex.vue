<template>
    <div>
        <div class="card">
                <div class="card-header">Items
                    <button @click="createOrEditItem()" style="margin-right:10px;" class="btn btn-sm btn-outline-primary float-right"><i class="fa fa-plus-circle"></i> New Item</button>
                </div>

                <div v-if="success" class="alert alert-primary fade show" role="alert" style="margin:2%;">
                    <strong> Your changes have been saved! </strong>
                    <button type="button" class="close" @click="success = false" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

            <div class="card-body">

                <div class="col-sm-4 mb-4 float-left collapse show" v-for="(items, index) in this.itemsByAisle">
                    <div class="card">
                        <a class="card-header no-style-anchor" data-toggle="collapse" :href="'#_' + items[0]">
                            {{index}}
                        </a>
                        <ul class="list-group list-group-flush collapse" :id="'_'+ items[0]">
                            <li v-if="index !== 0" v-for="(item, index) in items" class="list-group-item">
                                <span class="aisle-item">
                                    <i v-if="favorites.includes(item)" @click="removeFromFavorites(item)" class="fas fa-star fa-fw text-warning"></i>
                                    <i v-if="! favorites.includes(item)" @click="addToFavorites(item)" class="far fa-star fa-fw"></i>
                                    {{item.name}}
                                    <i @click="createOrEditItem(item)" class="fas fa-edit fa-fw"></i>
                                    <i @click="deleteItemModal(item)" class="fas fa-trash-alt fa-fw text-danger"></i>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>


            <modal-item :initial-item='{}' :initial-action="'Add'" :aisles='this.aisles' v-show="modalItemOpen" @close-item-modal="modalItemOpen = false"></modal-item>

            <modal-confirm-delete :model_type= "'items'" :model_id= 'itemBeingDeleted.id' :model_name="itemBeingDeleted.name" :redirect="false" @delete-success="removeItem"
                                  v-show="modalConfirmDeleteOpen" @close-confirm-delete-modal="modalConfirmDeleteOpen = false">

                <template v-slot:title> Confirm Delete </template>
                <template v-slot:body> Are you sure that you want to delete this item? This action <strong>cannot be undone</strong>.  </template>

            </modal-confirm-delete>

        </div>
    </div>
</template>


<script>
    export default {
        props: ['initialAvailableItems', 'aisles', 'initialFavoriteItems'],
        data() {
            return {
                items: this.initialAvailableItems,
                modalItemOpen: false,
                modalConfirmDeleteOpen: false,
                success: false,
                itemBeingDeleted: {id: 0, name: null},
                favorites: this.initialFavoriteItems,
            }
        },
        created() {
            Event.$on('item-added', this.addItem);
            Event.$on('item-edited', this.editItem);
        },
        destroyed() {
            Event.$off('item-added');
            Event.$off('item-edited');
        },
        methods:
            {
                addItem(item) {
                    this.items.push(item);
                },
                deleteItemModal(item) {
                    this.itemBeingDeleted = item;
                    this.modalConfirmDeleteOpen = true;
                },
                removeItem(id) {
                    let index = Object.keys(this.items).find(key => this.items[key].id === id);
                    this.$delete(this.items, index);
                },
                editItem(item) {
                    let itemToRemove = this.items.find(function(e) {
                        return e.id === item.id;
                    });
                    let index = this.items.indexOf(itemToRemove);
                    this.items.splice(index, 1, item);
                },
                createOrEditItem(item) {
                    this.modalItemOpen = true;
                    if (item) {
                        Event.$emit('open-item-modal-edit', item);
                    }
                },
                removeFromFavorites(item) {
                    let index = Object.keys(this.favorites).find(key => this.favorites[key].id === item.id);
                    this.$delete(this.favorites, index);
                },
                addToFavorites(item) {
                    this.favorites.push(item)
                },
            },
        computed:
            {
                itemsByAisle() {
                    let aisles = {};
                    let currentAisleName = "";

                    this.items.forEach(function (item) {
                        if (item.aisle.name !== currentAisleName) {
                            currentAisleName = item.aisle.name;
                            if (aisles[currentAisleName] === undefined) {
                                aisles[""+currentAisleName+""] = [item.aisle.id];
                            }
                        }
                        aisles[""+currentAisleName+""].push(item);
                    });

                    return aisles;
                },
            },
    }
</script>

<style>
    .no-style-anchor {
        text-decoration: none;
        color: inherit;
    }
    .no-style-anchor:hover {
        text-decoration: none;
        color: inherit;
    }

    i:hover {
        cursor: pointer;
    }

</style>
