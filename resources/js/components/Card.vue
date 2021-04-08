<template>
  <box
    ref="box"

    :width="90"
    :height="160"
    :thickness="2"

    :initialRotationY="(startReversed) ? 180 : 0"
    :posX="posX"
    :posY="posY"

    back="url(res/card/back.png)"
    :front="`url(res/card/front/${suit}.png), #fff`"
    :textFront="rank"

    left="#fff"
    right="#fff"
    top="#fff"
    bottom="#fff"

    @action="flip"
  />
</template>

<script>
import Box from '@/components/Box.vue'
export default {
  components: {
    Box
  },
  data() {
    return{

    }
  },
  props: {
    suit: {
      type: String,
      required: true
    },
    rank: {
      type: String,
      required: true
    },
    startReversed: {
      type: Boolean,
      default: true
    },
    posX: {
      type: Number,
      default: 500
    },
    posY: {
      type: Number,
      default: 100
    }
  },
  methods: {
    flip() {
      setTimeout(() => {
        this.$refs.box.rotationY += 90
        this.$refs.box.scale *= 1.5;
      }, 0 * this.$refs.box.animationStepTime)
      setTimeout(() => {
        this.$refs.box.rotationY += 90
        this.$refs.box.scale /= 1.5;
      }, 1 * this.$refs.box.animationStepTime)
    }
  }
}
</script>
