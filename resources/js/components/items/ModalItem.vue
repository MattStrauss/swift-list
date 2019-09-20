<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <form style="margin:2%;" @submit.prevent="submit" autocomplete="off">
                        <div class="modal-header">
                            <h5 class="modal-title">{{action}} Item</h5>
                            <button @click="clearItemDetails('AndCloseModal')" type="button" class="close" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div v-if="success" class="alert alert-primary fade show" role="alert">
                                <strong>Item {{action}}ed</strong>
                                <button type="button" class="close" @click="success = false" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" ref="name" name="name" id="name" v-model="item.name" autofocus>
                                    <div v-if="errors && errors.name" class="text-danger">{{ errors.name[0] }}</div>
                                </div>

                                <div class="form-group">
                                    <label for="aisle">Aisle</label>
                                    <select class="form-control" name="aisle_id" id="aisle" v-model="item.aisle_id">
                                        <option> </option>
                                        <option v-for="aisle in aisles" :value="aisle.id">
                                            {{aisle.name}}
                                        </option>
                                    </select>
                                    <div v-if="errors && errors.aisle_id" class="text-danger">{{ errors.aisle_id[0] }}</div>
                                </div>

                            <input type="hidden" name="item_id" value="" v-model="item.id">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" @click="clearItemDetails(('AndCloseModal'))">Cancel</button>
                            <button @click="submit" type="button" class="btn btn-outline-primary">
                                <span v-show="processing" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                {{action}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        props: ['initialItem', 'initialAction', 'aisles'],
        data() {
            return {
                processing: false,
                errors: {},
                success: false,
                item: this.initialItem,
                action: this.initialAction,
            }
        },
        created() {
            Event.$on('open-item-modal-edit', this.updateItem);
        },
        beforeDestroy() {
            Event.$off('open-item-modal-edit');
        },
        methods: {
            submit() {
                this.processing = true;
                this.errors = {};
                let uri = "/items/";
                let method = "post";
                if (this.action === 'Edit') {
                    uri = '/items/' + this.item.id;
                    method = "put";
                }
                axios({
                    method: method,
                    url: uri,
                    data: this.item
                }).then(response => {
                    this.processing = false;
                    this.success = true;
                    setTimeout(() => {this.success = false;}, 2000);
                    if (this.action === "Add") {
                        this.clearItemDetails();
                        this.$refs.name.focus();
                        Event.$emit('item-added', response.data);
                    } else {
                        Event.$emit('item-edited', response.data);
                    }
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.processing = false;
                        this.errors = error.response.data.errors || {};
                    }
                });
            },
            updateItem(item) {
                this.action = 'Edit';
                this.item.name = item.name;
                this.item.aisle_id = item.aisle.id;
                this.item.id = item.id;
            },
            clearItemDetails(closeModal = false) {
                this.item['name'] = "";
                this.item['aisle_id'] = "";
                this.item['id'] = "";
                if (closeModal) {
                    this.closeItemModal();
                }
            },
            closeItemModal() {
                this.$emit('close-item-modal');
                this.success = false;
                this.action = "Add";
            }
        }
    }
</script>

<style scoped>
    .modal-mask {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        display: table;
        transition: opacity .3s ease;
    }

    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }

    .modal-container {
        width: 400px;
        margin: 0 auto;
        padding: 10px 15px;
        background-color: #fff;
        border-radius: 2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
        transition: all .3s ease;
        font-family: Helvetica, Arial, sans-serif;
    }

    .modal-header h3 {
        margin-top: 0;
        color: #42b983;
    }

    .modal-body {
        margin: 20px 0;
        padding-top: .25rem;
        padding-bottom: .25rem;
    }

    .modal-footer {
        justify-content: space-between;
    }

    .modal-enter {
        opacity: 0;
    }

    .modal-leave-active {
        opacity: 0;
    }

    .modal-enter .modal-container,
    .modal-leave-active .modal-container {
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
    }

    .alert {
        margin-top: -10px;
    }

</style>
