<template>
  <div>
    <div class="row">
      <div class="mx-auto mt-5">
        <h1> Hello, {{nick}} in {{game.name}} </h1>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    nick: {
      type: String
    },
    game: {
      type: Object
    }
  }
}
</script>
