<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div v-if="error" class="alert alert-danger fade show" role="alert" style="margin:2%;">
                        <strong>Error:</strong> {{error}}
                        <button type="button" class="close" @click="error = ''" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-header">
                                <h5 class="modal-title"><slot name="title"></slot></h5>
                                <button @click="$emit('close-confirm-delete-modal')" type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <slot name="body"></slot>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" @click="$emit('close-confirm-delete-modal')">Cancel</button>
                                <button @click="submit" type="button" class="btn btn-outline-danger">
                                    <span v-show="processing" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
    </transition>
</template>

<script>
    export default {
        props: ['model_type', 'model_id'],
        data() {
            return {
                processing: false,
                error: "",
            }
        },
        methods: {
            submit() {
                this.processing = true;
                axios.delete('/'+this.model_type+'/' + this.model_id).then(response => {
                    this.processing = false;
                    window.location = response.data.redirect;
                }).catch(error => {
                    this.processing = false;
                    this.error = error.response.data.error;
                });
            },
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

</style>

