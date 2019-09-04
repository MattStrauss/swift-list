<template>
    <div>
        <div class="card-body">
            <div v-if="success" class="alert alert-primary fade show" role="alert">
                <strong>Saved!</strong> Your changes have been saved.
                <button type="button" class="close" @click="success = false" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <p class="text-center mb-4">Simply drag and drop the aisles into your desired order.</p>
            <div>
                <ul class="list-group list-group-flush">
                    <draggable v-model="aisles" @update="submit">
                        <li v-for="(aisle, index) in aisles" :key="aisle.id" class="list-group-item aisles-li"> <span class="badge badge-secondary">{{index + 1}}</span> {{aisle.name}}</li>
                    </draggable>
                </ul>
                <br><br>
            </div>
        </div>
    </div>
</template>


<script>
    import draggable from 'vuedraggable';
    export default {
        props: ['initialAisles'],
        components: {
            draggable,
        },
        data() {
            return {
                aisles: this.initialAisles,
                success: false,
            }
        },
        methods:
            {
                submit() {
                axios.post('/aisles/', this.aisles).then(response => {
                    this.success = true;
                    setTimeout(() => {this.success = false;}, 2000);
                }).catch(error => {
                    this.error = error.response.data.error;
                });
                },
            },
        computed:
            {

            }
    }
</script>

<style>
    .aisles-li:hover {cursor: move;}
</style>
