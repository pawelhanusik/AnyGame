<template>
 <Box
    ref="box"
    :componentID="componentID"
    :gameID="gameID"

    front="url(res/dice/1.png)"
    top="url(res/dice/2.png)"
    left="url(res/dice/3.png)"
    right="url(res/dice/4.png)"
    bottom="url(res/dice/5.png)"
    back="url(res/dice/6.png)"

    :width="size"
    :height="size"
    :thickness="size"

    :initialRotationX="(this.randomNumberOnStart) ? Math.floor(Math.random() * 4 + 1) * 90 : rotX"
    :initialRotationY="(this.randomNumberOnStart) ? Math.floor(Math.random() * 4 + 1) * 90 : rotY"
    :initialRotationZ="(this.randomNumberOnStart) ? Math.floor(Math.random() * 4 + 1) * 90 : rotZ"
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
    componentID: {
      type: Number,
      required: true
    },
    gameID: {
      type: Number,
      required: true
    },

    size: {
      type: Number,
      default: 64
    },
    randomNumberOnStart: {
      type: Boolean,
      default: false
    },
    posX: {
      type: Number,
      default: 500
    },
    posY: {
      type: Number,
      default: 100
    },
    rotX: {
      type: Number,
      default: 0
    },
    rotY: {
      type: Number,
      default: 0
    },
    rotZ: {
      type: Number,
      default: 0
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
    },
    updateParams(params) {
      if (!Array.isArray(params) || params.length < 5 ) {
        console.error("updateParams() extected 5 items in params array, less given")
        return
      }

      if (params[0]) this.$refs.box.positionX = params[0]
      if (params[1]) this.$refs.box.positionY = params[1]
      if (params[2]) this.$refs.box.rotationX = params[2]
      if (params[3]) this.$refs.box.rotationY = params[3]
      if (params[4]) this.$refs.box.rotationZ = params[4]
      this.$refs.box.recentChanges = {}
    }
  }
}
</script>
