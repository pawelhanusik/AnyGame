<template>
 <Box
    ref="box"

    front="url(res/dice/1.png)"
    top="url(res/dice/2.png)"
    left="url(res/dice/3.png)"
    right="url(res/dice/4.png)"
    bottom="url(res/dice/5.png)"
    back="url(res/dice/6.png)"

    :width="size"
    :height="size"
    :thickness="size"

    :initialRotationX="(this.randomNumberOnStart) ? Math.floor(Math.random() * 4 + 1) * 90 : 0"
    :initialRotationY="(this.randomNumberOnStart) ? Math.floor(Math.random() * 4 + 1) * 90 : 0"
    :initialRotationZ="(this.randomNumberOnStart) ? Math.floor(Math.random() * 4 + 1) * 90 : 0"
    :posX="posX"
    :posY="posY"

    :animationStepTime="300"

    @action="roll"
  />
</template>

<script>

import Box from '@/components/Box.vue'

export default {
  components: {
    Box
  },
  props: {
    size: {
      type: Number,
      default: 64
    },
    randomNumberOnStart: {
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
  computed: {
    number() {
      return this.$refs.box.visibleSide
    }
  },
  methods: {
    // actions
    roll() {
      const rx = Math.floor(Math.random() * 4 + 1) * 90
      const ry = Math.floor(Math.random() * 4 + 1) * 90
      const rz = Math.floor(Math.random() * 4 + 1) * 90

      setTimeout(() => {
        this.$refs.box.rotationX += rx/2
        this.$refs.box.rotationY += ry/2
        this.$refs.box.rotationZ += rz/2
        this.$refs.box.scale *= 1.5;
      }, 0 * 300)
      setTimeout(() => {
        this.$refs.box.rotationX += rx/2
        this.$refs.box.rotationY += ry/2
        this.$refs.box.rotationZ += rz/2
        this.$refs.box.scale /= 1.5;
      }, 1 * 300)
    }
  }
}
</script>
