<template>
  <div>
    <div class="row">
      <div class="mx-auto mt-5">
        <h1> Game {{name}} created </h1>
      </div>
    </div>
    <div class="row">
      <div class="mx-auto mt-5">
        <h4 v-if="password"> Password: {{password}} </h4>
        <h4 v-else> Game is public </h4>
      </div>
    </div>

    <div class="row">
      <div class="mx-auto">
        <h4> URL: <inertia-link :href="url"> {{urlAbsolute}} </inertia-link> </h4>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    name: {
      type: String,
      default: ""
    },
    url: {
      type: String,
      default: ""
    },
    password: {
      type: String,
      default: ""
    }
  },
  computed: {
    urlAbsolute() {
      return document.location.protocol + '//' + document.location.host + this.url
    }
  }
}
</script>
