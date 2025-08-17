<template>

    <div>

        <Head>
            <title>Brand - Admin</title>
        </Head>
        <div class="d-flex justify-content-between align-items-center">
            <h1 color="text-center"> All Category</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#exampleModalForCreate">Create</button>
        </div>

        <!-- Modal for creating a new category -->
        <div class="modal fade" id="exampleModalForCreate" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header ">
                        <h1 class="modal-title fs-5 text-center">Create a new category</h1>
                    </div>

                    <div class="modal-body">

                        <form @submit.prevent="submit">

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Enter name"
                                    v-model="form.name">
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" name="image" id="" class="form-control" @change="uploadImage">
                            </div>
                            <div class="mb-3" v-if="form.preview">
                                <img :src="form.preview" style="width: 200px; height: 200px">
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label for="status" class="form-label">Status</label>
                                <input type="checkbox" name="status" class="form-check" v-model="form.status">
                            </div>


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
        <div>

            <div class="col-md-6 mb-3 start-end">
                <input type="text" class="form-control" v-model="searchValue"></input>
            </div>

            <EasyDataTable buttons-pagination alternating :headers="header" :items="item" theme-color="#009A31"
                :rows-per-page="10" table-class-name="customize-table" :search-field="searchField"
                :search-value="searchValue" show-index>

                <template #item-image="item">
                    <img :src="'storage/' + item.image" style="width: 50px; height: 50px">
                </template>
                <template #item-status="item">
                    <span :class="item.is_active == 1 ? 'text-success' : 'text-warning'">{{ item.is_active == 1 ?
                        'Active' :
                        'Inactive' }}</span>
                </template>
                <template #item-number="{  id }">
                    <div class="d-flex align-items-center my-2">
                        <button class="btn btn-sm btn-secondary me-2" @click="categoryUpdateClick(id)"><i
                            class="fa-regular fa-pen-to-square"></i>
                        </button>
                        <Link class="btn btn-sm btn-danger" href="#" @click="itemClick(id)"><i
                            class="fa-regular fa-trash-can"></i></Link>
                    </div>
                </template>


            </EasyDataTable>
        </div>

        <!-- Modal for updating a category -->
        <div class="modal fade" id="exampleModalForUpdate" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header ">
                        <h1 class="modal-title fs-5 text-center">Update category</h1>
                    </div>

                    <div class="modal-body">

                        <form @submit.prevent="updateSubmit">

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Enter name"
                                    v-model="editForm.name">
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" name="image" id="" class="form-control" @change="uploadImageForUpdate">
                            </div>
                            <div class="mb-3">
                                <img :src="editForm.preview ? editForm.preview : 'storage/' + editForm.oldImage" style="width: 200px; height: 200px">
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label for="status" class="form-label">Status</label>
                                <input type="checkbox" name="status" class="form-check" v-model="editForm.status">
                            </div>


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" :disabled="editForm.processing">Add</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { Head, router, usePage, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { Modal } from 'bootstrap';
import { ref } from 'vue';



defineOptions({
    layout: AdminLayout,
});

const page = usePage();

const form = useForm({
    name: '',
    image: '',
    preview: '',
    status: false,
});

const uploadImage = (e) => {
    form.image = e.target.files[0];
    form.preview = URL.createObjectURL(e.target.files[0]);
}


const submit = () => {

    if (form.name == '') {
        new Notify({
            title: 'Error',
            text: 'Name is required',
            status: 'error'
        })
        return
    } else if (form.image == '') {
        new Notify({
            title: 'Error',
            text: 'Image is required',
            status: 'error'
        })
        return
    }
    form.post('/category', {
        onSuccess: () => {
            if (page.props.flash.success) {
                new Notify({
                    status: 'success',
                    title: page.props.flash.success.message,
                    autotimeout: 2000,
                })

                form.reset();
                const modalEl = document.getElementById('exampleModalForCreate');
                const modal = Modal.getInstance(modalEl) || new Modal(modalEl);
                modal.hide();

                router.get('/categories')

            } else if (page.props.flash.error) {
                new Notify({
                    status: 'error',
                    title: page.props.flash.error.message,
                    autotimeout: 2000,
                })
            }
        }
    });
}


const header = [
    { text: 'Name', value: 'name' },
    { text: 'Image', value: 'image' },
    { text: 'Status', value: 'status' },
    { text: 'Action', value: 'number' },
]

const item = ref(page.props.categories);

const searchField = ['name'];
const searchValue = ref('');


// delete 

const itemClick = (id) => {
    router.delete('/category/' + id, {
        onSuccess: () => {
            if (page.props.flash.success) {
                new Notify({
                    status: 'success',
                    title: page.props.flash.success.message,
                    autotimeout: 2000,
                })
                router.get("/categories");
            } else if (page.props.flash.error) {
                new Notify({
                    status: 'error',
                    title: page.props.flash.error.message,
                    autotimeout: 2000,
                })
            }
        },

        onError: () => {
            new Notify({
                status: 'error',
                title: page.props.flash.error.message,
                autotimeout: 2000,
            })
        }
    })
}


// update
const editForm = useForm({
    id: '',
    name: '',
    image: '',
    preview: '',
    status: false,
    oldImage: '',
});
const categoryUpdateClick = (id) => {
    item.value.map((item) => {
        if (item.id == id) {
            editForm.id = item.id;
            editForm.name = item.name;
            editForm.oldImage = item.image;
            editForm.status = item.is_active == 1 ? true : false;

        }
    })
    const modalEl = document.getElementById('exampleModalForUpdate');
    const modal = Modal.getInstance(modalEl) || new Modal(modalEl);
    modal.show();
}

const uploadImageForUpdate = (e) => {
    editForm.image = e.target.files[0];
    editForm.preview = URL.createObjectURL(e.target.files[0]);
}

const updateSubmit = () => {
    if (editForm.name == '') {
        new Notify({
            title: 'Error',
            text: 'Name is required',
            status: 'error'
        })
        return
    }
    editForm.post('/category/' + editForm.id, {
        forceFormData: true,
        onSuccess: () => {
            if (page.props.flash.success) {
                new Notify({
                    status: 'success',
                    title: page.props.flash.success.message,
                    autotimeout: 2000,
                })

                editForm.reset();
                const modalEl = document.getElementById('exampleModalForUpdate');
                const modal = Modal.getInstance(modalEl) || new Modal(modalEl);
                modal.hide();

                router.get('/categories')

            } else if (page.props.flash.error) {
                new Notify({
                    status: 'error',
                    title: page.props.flash.error.message,
                    autotimeout: 2000,
                })
            }
        },
        onError: () => {
            new Notify({
                status: 'error',
                title: page.props.flash.error.message,
                autotimeout: 2000,
            })
        }
    });
}

</script>

<style lang="scss" scoped></style>