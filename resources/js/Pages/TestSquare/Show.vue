<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Test Squares</h1>
    <inertia-link as="button" href="/"> Back to list </inertia-link>
    <p> This square is {{square.color}} </p>
    <div
        :style="`
            background-color: ${square.color};
            width: 64px;
            height: 64px;
            border: 3px solid black;
        `"
    />
  </div>
</template>

<script>
export default {
  metaInfo: { title: 'Dashboard' },
  props: {
    square: Object
  }
}
</script>
