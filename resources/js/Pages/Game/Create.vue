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
          <h3 class="text-center"> Game settings </h3>
          <div class="mb-3">
            <label for="name" class="form-label"> Name </label>
            <input type="text" class="form-control" id="name" v-model="form.name" autocomplete="off">
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="public" v-model="form.public">
            <label class="form-check-label" for="public">Make public</label>
          </div>

          <hr/>

          <h3 class="text-center"> Game components </h3>
          <h4> Table </h4>
          <div v-for="c in componentTyoes" :key="tableName(c)" class="mb-3 form-row">
            <label :for="tableName(c)" class="col"> {{ c }} </label>
            <input type="number" min=0 class="col" :id="tableName(c)" v-model="form[tableName(c)]" autocomplete="off">
          </div>

          <h4> For each player </h4>
          <div v-for="c in componentTyoes" :key="handName(c)" class="mb-3 form-row">
            <label :for="handName(c)" class="col"> {{ c }} </label>
            <input type="number" min=0 class="col" :id="handName(c)" v-model="form[handName(c)]" autocomplete="off">
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
  props: {
    componentTyoes: {
      type: Array,
      required: true
    }
  },
  methods: {
    tableName(c) {
      return `table_${c}`
    },
    handName(c) {
      return `hand_${c}`
    },
    submit() {
      this.$inertia.post('/create', this.form)
    },
  },
}
</script>
