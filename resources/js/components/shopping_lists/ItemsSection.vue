<template>
    <div class="clearfix">
        <h6> {{ (this.context !== 'recipe') ? 'Items' : 'Ingredients' }}
            <a @click="createOrEditItem()" data-toggle="tooltip" title="Add item" class="cursor-pointer">
                <i class="fas fa-plus-circle text-secondary add-able-icon"></i>
            </a>
            <a v-if="this.context !== 'recipe'" @click="showAislesToggle()" class="item-delete-able toggle-aisles text-muted small">
                <i :class="{'fas fa-eye-slash': showAisles, 'fas fa-eye': ! showAisles}"></i> Aisles
            </a>
            <a v-show="this.context !== 'recipe'" v-if="this.favoriteItemsNotEmpty" @click="addFavoriteItemsToggle()" class="item-delete-able toggle-aisles text-muted small">
                <i :class="{'fas fa-times': this.favoriteItemsAllOnList, 'fas fa-plus': ! this.favoriteItemsAllOnList}"></i> Favorite Items
            </a>
        </h6>
        <auto-complete :items="this.items" :isAsync="false" :model="'item'" :placeHolder="(this.context !== 'recipe') ? 'Search Items...' : ' Search for Ingredients...'" @item-added="addItem" @modal-item-open="createOrEditItem"></auto-complete>

        <div v-show="showAisles" class="col-sm-4 mb-4 float-left collapse show" v-for="(items, index) in this.itemsByAisle">
            <div class="card">
                <a class="card-header no-style-anchor" data-toggle="collapse" :href="'#_' + items[0].aisle_id">
                    {{index}}
                </a>
                <ul class="list-group list-group-flush collapse" :id="'_'+ items[0].aisle_id">
                    <li v-for="(item, index) in items" class="list-group-item">
                        <span v-if="! includedItems.find(included => included.id === item.id)">
                            <span @click="addItem(item)" class="item-add-able">
                                <i class="fa fa-plus fa-fw add-able-icon"></i> {{item.name}}
                            </span>
                            <i @click="createOrEditItem(item)" class="fas fa-edit fa-fw"></i>
                        </span>
                        <span v-else class="aisle-item-delete-able">
                            <i @click="deleteItem(null, item.id)" class="fa fa-times fa-fw"></i> {{item.name}}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="clearfix"></div>


        <h6> {{ (this.context !== 'recipe') ? 'Items On List' : 'Included Ingredients' }} <small v-if="includedItems.length !== 0">({{includedItems.length}})</small></h6>
        <ul v-if="includedItems.length !== 0" class="items items-on-list">
            <li v-for="(item, index) in includedItems" :index="item.id" class="item-delete-able">
                <i class="fa fa-times fa-hover-show fa-fw" @click="deleteItem(index)"></i> <i class="fa fa-times-circle fa-hover-hidden fa-fw"></i> {{ item.name }}</li>
        </ul>
        <p v-else class="text-muted"> {{ (this.context !== 'recipe') ? 'No items on list yet...' : 'No ingredients...' }} </p>

        <modal-item :initial-item='{}' :initial-action="'Add'" :aisles='this.aisles' v-show="modalItemOpen" @close-item-modal="modalItemOpen = false"></modal-item>

    </div>
</template>


<script>
    export default {
        props: ['availableItems', 'initialIncludedItems', 'aisles', 'context'],
        data() {
            return {
                includedItems: this.initialIncludedItems,
                showAisles: false,
                modalItemOpen: false,
                items: this.availableItems,
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
                    if (this.includedItems.find(included => included.id === item.id) === undefined) {
                        this.includedItems.push(item);
                        this.saveList();
                    }
                    if (this.items.find(included => included.id === item.id) === undefined) {
                        this.items.push(item);
                    }
                },
                deleteItem(index, id = null) {
                    if (id !== null)  {
                        index = Object.keys(this.includedItems).find(key => this.includedItems[key].id === id);
                    }
                    this.$delete(this.includedItems, index);
                    this.saveList();
                },
                showAislesToggle() {
                    this.showAisles = ! this.showAisles;
                },
                addFavoriteItemsToggle () {
                    if (! this.favoriteItemsAllOnList) {
                        this.includedItems.push(...this.favoriteItemsNotOnList);
                        this.saveList();
                    } else {
                        this.favoriteItems.forEach(item => (this.includedItems.find(included => included.id === item.id)) ? this.deleteItem(null, item.id) : false);
                    }
                },
                editItem(item) {
                    let itemToRemove = this.items.find(i => i.id === item.id);
                    let index = this.items.indexOf(itemToRemove);
                    this.items.splice(index, 1, item);
                },
                createOrEditItem(item) {
                    this.modalItemOpen = true;
                    if (item) {
                        Event.$emit('open-item-modal-edit', item);
                    }
                },
                saveList() {
                    this.$emit('save-list');
                }
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
                                aisles[""+currentAisleName+""].shift(); // seems silly, but without the above line, I cannot get this to work as expected
                            }
                        }
                        aisles[""+currentAisleName+""].push(item);

                    });

                    Object.keys(aisles).forEach(item => aisles[item].sort((a, b) => (a.name.toLowerCase() < b.name.toLowerCase()) ? -1 : 1));

                    return aisles;
                },
                favoriteItems() {
                    let favorites = [];
                    this.items.forEach(item => (item.favorite) ? favorites.push(item) : false);

                    return favorites;
                },
                favoriteItemsNotEmpty() {
                    return this.favoriteItems.length > 0;
                },
                favoriteItemsNotOnList() {
                    return this.favoriteItems.filter(item => ! this.includedItems.find(function(included) {
                        return item.id === included.id
                    }));
                },
                favoriteItemsAllOnList() {
                    return this.favoriteItemsNotOnList.length === 0;
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

    .add-able-icon:hover {
        cursor: pointer;
        color: #3490dc !important;
    }

    .item-add-able:hover {
        color: #3490dc;
        cursor: pointer;
    }

    .aisle-item-delete-able {
        pointer-events: none;
        cursor: none;
        color: #6c757d;
    }

    .aisle-item-delete-able:hover {
        color: #e3342f;
    }

    .aisle-item-delete-able > i {
        pointer-events: auto;
        cursor: pointer;
    }

    .item-delete-able.toggle-aisles {
        pointer-events: auto;
        cursor: pointer;
    }

    .item-delete-able.toggle-aisles:hover {
        color: #3490dc !important;
    }
</style>
