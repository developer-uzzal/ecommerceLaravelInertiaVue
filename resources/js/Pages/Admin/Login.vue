<template>

  <Head title="Admin Login" />
  <div class="container mt-5 shadow p-4" style="max-width: 400px;">
    <h1 class="mb-4 text-center">Admin Login</h1>
    <form @submit.prevent="submit">
      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" placeholder="Enter email" v-model="form.email" />
      </div>

      <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" placeholder="Enter password"
          v-model="form.password" />
      </div>

      <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
        Login
      </button>
    </form>
  </div>
</template>

<script setup>
import { Head, useForm,usePage } from '@inertiajs/vue3';
import Notify from 'simple-notify';

const page = usePage();

const form = useForm({
  email: '',
  password: '',
})

const submit = () => {

  if (form.email == '') {
    new Notify({
      title: 'Error',
      text: 'Email is required',
      status: 'error'
    })
    return
  } else if (form.password == '') {
    new Notify({
      title: 'Error',
      text: 'Password is required',
      status: 'error'
    })
    return
  } else {

    form.post('/login', {
      onSuccess: () => {

        if (page.props.flash.error) {
          new Notify({
            status: 'error',
            title: page.props.flash.error.message,
            autotimeout: 2000,
          })

          form.password = '';

        } else if (page.props.flash.success) {
          new Notify({
            status: 'success',
            title: page.props.flash.success.message,
            autotimeout: 2000,
          })

          form.reset();
          
        }


      },
      onError: () => {
        new Notify({
          status: 'error',
          title: 'Login failed',
          autotimeout: 2000,
        })
      }
    });

  }
}
</script>

<style lang="scss" scoped></style>
