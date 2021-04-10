<template>
  <div>
    <div class="row">
      <div class="mx-auto mt-5">
        <h1>Any Game</h1>
      </div>
    </div>
    <div class="row">
      <div class="mx-auto">
        <inertia-link as="button" href="create" class="btn btn-primary">Create Game</inertia-link>
      </div>
    </div>

    <dice v-for="i in 7" :key="i" :posX="randomX()" :posY="randomY()" style="z-index: -1000" ref="dices" />
  </div>
</template>

<script>
import Dice from '@/components/Dice.vue'
import Card from '@/components/Card.vue'

let rollDiceIntervalID = null
export default {
  components: {
    Dice,
    Card
  },
  created() {
    rollDiceIntervalID = setInterval(() => {
      const randomID = Math.floor(Math.random() * this.$refs.dices.length)
      this.$refs.dices[randomID].roll()
    }, 3000)

    Echo.channel('test-channel.1')
      .listen('TestEvent', (e) => {
        console.log("ECHO TestEvent", e)
      })
  },
  destroyed() {
    if (rollDiceIntervalID !== null) {
      clearInterval(rollDiceIntervalID)
    }
  },
  methods: {
    randomX() {
      return Math.random() * (screen.width - 192) + 64
    },
    randomY() {
      return Math.random() * (screen.height / 4 * 3 - 64) + 64
    }
  }
}
</script>
