<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Test Squares</h1>
    <p class="mb-8 leading-normal">Hey there! The list is below:</p>
    <div>
      <div v-for="(s, key) of squares" :key="key">
        <inertia-link as="button" :href="`${s.id}`">
          [{{s.id}}] {{s.color}}
        </inertia-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  metaInfo: { title: 'Dashboard' },
  props: {
    squares: Array
  }
}
</script>
