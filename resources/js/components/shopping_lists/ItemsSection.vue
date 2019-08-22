<template>
    <div>
        <h6>Items
            <a @click="createOrEditItem()" data-toggle="tooltip" title="Add item" class="cursor-pointer">
                <i class="fas fa-plus-circle text-secondary add-able-icon"></i>
            </a>
            <a @click="showAislesToggle" class="item-delete-able toggle-aisles text-muted small">
                <i :class="{'fas fa-eye-slash': showAisles, 'fas fa-eye': ! showAisles}"></i> Aisles
            </a>
        </h6>
        <auto-complete :items="availableItems" :isAsync="false" :model="'item'" :placeHolder="'Search Items...'" @item-added="addItem"></auto-complete>

        <div v-show="showAisles" class="col-sm-4 mb-4 float-left collapse show" v-for="(items, index) in this.itemsByAisle">
            <div class="card">
                <a class="card-header no-style-anchor" data-toggle="collapse" :href="'#_' + items[0]">
                    {{index}}
                </a>
                <ul class="list-group list-group-flush collapse" :id="'_'+ items[0]">
                    <li v-if="index !== 0" v-for="(item, index) in items" class="list-group-item">
                        <span v-if="! includedItems.includes(item)" @click="addItem(item)" class="item-add-able">
                            <i class="fa fa-plus fa-fw add-able-icon"></i> {{item.name}}
                        </span>
                        <span v-else class="aisle-item-delete-able">
                            <i @click="deleteItem(null, item.id)" class="fa fa-times fa-fw"></i> {{item.name}}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="clearfix"></div>


        <h6> Items On List <small v-if="includedItems.length !== 0">({{includedItems.length}})</small></h6>
        <ul v-if="includedItems.length !== 0" class="items">
            <li v-for="(item, index) in includedItems" :index="item.id" class="item-delete-able">
                <i class="fa fa-times fa-hover-show fa-fw" @click="deleteItem(index)"></i> <i class="fa fa-times-circle fa-hover-hidden fa-fw"></i> {{ item.name }}</li>
        </ul>
        <p v-else class="text-muted"> No items on list yet...</p>

        <modal-item :initial-item='{}' :initial-action="'Add'" :aisles='this.aisles' v-show="modalItemOpen" @close-item-modal="modalItemOpen = false"></modal-item>

    </div>
</template>


<script>
    export default {
        props: ['availableItems', 'initialIncludedItems', 'aisles'],
        data() {
            return {
                includedItems: this.initialIncludedItems,
                showAisles: false,
                modalItemOpen: false,
            }
        },
        created() {
            Event.$on('item-added', this.addItem);
        },
        destroyed() {
            Event.$off('item-added');
        },
        methods:
            {
                addItem(item) {
                    this.includedItems.push(item);
                    this.saveList();
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
                createOrEditItem(item) {
                    this.modalItemOpen = true;
                    if (item) {
                        Event.$emit('open-item-modal-edit', item);
                    }
                },
                saveList() {
                    this.$emit('save-list')
                }
            },
        computed:
            {
                itemsByAisle() {
                    let aisles = {};
                    let currentAisleName = "";

                    this.availableItems.forEach(function (item) {
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
