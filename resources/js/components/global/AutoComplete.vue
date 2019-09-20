<template>
    <div class="autocomplete">
        <div class="input-group" :class="{'mb-0':isOpen, 'mb-4': ! isOpen}">
            <div class="input-group-prepend">
                <span class="input-group-text" id="search"><i class="fas fa-search fa-fw"></i></span>
            </div>
            <input type="text" class="form-control" @input="onChange" v-model="search"
                   @keydown.down="onArrowDown" @keydown.up="onArrowUp" @keydown.enter="onEnter" :placeholder="placeHolder">
        </div>
        <ul id="autocomplete-results" v-show="isOpen" class="autocomplete-results">
            <li v-if="isLoading" class="loading">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </li>
            <li v-else v-for="(result, i) in results" :key="i" @click="setResult(result)" class="autocomplete-result" :class="{ 'is-active': i === arrowCounter }">
                {{ result.name }} - {{(model === "recipe") ? result.category.name : result.aisle.name}}
            </li>
            <li v-if="this.results.length < 1 && this.model === 'item' && ! this.isLoading" class="autocomplete-result">
                <span @click="addNewItem">Add New item</span>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: {
            items: {
                type: Array,
                required: false,
                default: () => [],
            },
            isAsync: {
                type: Boolean,
                required: false,
                default: false,
            },
            model: {
                type: String,
                required: true,
                default: false,
            },
            placeHolder: {
                type: String,
                required: false,
                default: false,
            },
        },

        data() {
            return {
                isOpen: false,
                results: [],
                search: '',
                isLoading: false,
                arrowCounter: -1,
            };
        },
        created() {
            document.addEventListener('click', this.handleClickOutside)
        },
        destroyed() {
            document.removeEventListener('click', this.handleClickOutside)
        },
        watch: {
            items: function (val, oldValue) {
                if (val.length !== oldValue.length) {
                    this.results = val;
                    this.isLoading = false;
                }
            },
        },
        methods: {
            onChange() {
                this.$emit("input", this.search);
                if (this.isAsync) {
                    this.isLoading = true;
                } else {
                    this.filterResults();
                    this.isOpen = true;
                }
            },

            filterResults() {
                let secondarySearchTerm = (this.model === "recipe") ? "category" : "aisle";
                this.results = this.items.filter((item) => {
                    return item.name.toLowerCase().indexOf(this.search.toLowerCase()) > -1 || item[secondarySearchTerm].name.toLowerCase().indexOf(this.search.toLowerCase()) > -1;
                });
            },
            setResult(result) {
                this.search = '';
                let eventName = (this.model === "recipe") ? "recipe-added" : "item-added";
                this.$emit(eventName, result);
                this.arrowCounter = -1;
                this.isOpen = false;
            },
            onArrowDown() {
                if (this.arrowCounter < this.results.length) {
                    this.arrowCounter = this.arrowCounter + 1;
                }
            },
            onArrowUp() {
                if (this.arrowCounter > 0) {
                    this.arrowCounter = this.arrowCounter -1;
                }
            },
            onEnter() {
                if (this.arrowCounter < 0) { this.arrowCounter = 0; }
                this.setResult(this.results[this.arrowCounter]);
            },
            handleClickOutside(evt) {
                if (!this.$el.contains(evt.target)) {
                    this.isOpen = false;
                    this.arrowCounter = -1;
                }
            },
            addNewItem() {
                this.$emit('modal-item-open');
                this.search = "";
                this.arrowCounter = -1;
                this.isOpen = false;
            }
        },
    };
</script>

<style>
    .autocomplete {
        position: relative;
    }

    .autocomplete-results {
        padding: 0;
        margin: 0;
        border: 1px solid #eeeeee;
        height: 160px;
        overflow: auto;
        width: 100%;
    }

    .autocomplete-result {
        list-style: none;
        text-align: left;
        padding: 4px 2px;
        cursor: pointer;
    }

    .autocomplete-result.is-active,
    .autocomplete-result:hover {
        background-color: #3490dc;
        color: white;
    }

</style>
