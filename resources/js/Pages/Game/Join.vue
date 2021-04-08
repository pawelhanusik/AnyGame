<template>
  <div>
    <div class="row">
      <div class="mx-auto mt-5">
        <h1> Game {{game.name}} </h1>
      </div>
    </div>
    <div class="row">
      <div class="mx-auto mt-5">
        <div v-if="error" class="text-danger"> {{error}} </div>
        <form @submit.prevent="submit">
          <div class="mb-3">
            <label for="nick" class="form-label"> Nick </label>
            <input type="text" class="form-control" id="nick" v-model="form.nick" autocomplete="off">
          </div>
          <div v-if="!isPublic" class="mb-3">
            <label for="p" class="form-label"> Game password </label>
            <input type="text" class="form-control" id="p" v-model="form.p" autocomplete="off">
          </div>
          <button type="submit" class="btn btn-primary">Connect</button>
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
        nick: '',
        p: ''
      }
    }
  },
  props: {
    game: {
      type: Object
    },
    nick: {
      type: String,
      default: ""
    },
    p: {
      type: String,
      default: ""
    },
    isPublic: {
      type: Boolean,
      default: false
    },
    error: {
      type: String,
      default: null
    }
  },
  created() {
    this.form.nick = this.nick
    this.form.p = this.p
  },
  methods: {
    submit() {
      this.$inertia.get('/' + this.game.id, this.form)
    }
  }
}
</script>
