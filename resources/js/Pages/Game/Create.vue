<template>
  <div>
    <div class="row">
      <div class="mx-auto mt-5">
        <h1> Create new game </h1>
      </div>
    </div>
    <div class="row">
      <div class="mx-auto mt-5">
        <form @submit.prevent="submit">
          <div class="mb-3">
            <label for="name" class="form-label"> Name </label>
            <input type="text" class="form-control" id="name" v-model="form.name" autocomplete="off">
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="public" v-model="form.public">
            <label class="form-check-label" for="public">Make public</label>
          </div>
          <button type="submit" class="btn btn-primary">Create</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        name: '',
        public: false,
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.post('/create', this.form)
    },
  },
}
</script>
