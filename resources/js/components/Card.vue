<template>
  <box
    ref="box"
    :componentID="componentID"
    :gameID="gameID"

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
    @client_action="client_flip"
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
    componentID: {
      type: Number,
      required: true
    },
    gameID: {
      type: Number,
      required: true
    },

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
    flip(orientation) {
      let ry = null
      if (this.$refs.box.rotationY == 180 && orientation == 0) {
        ry = 0
      }
      if (this.$refs.box.rotationY == 0 && orientation == 5) {
        ry = 180
      }
      
      if (ry !== null) {
        setTimeout(() => {
          this.$refs.box.rotationY = 90
          this.$refs.box.scale *= 1.5;
        }, 0 * this.$refs.box.animationStepTime)
        setTimeout(() => {
          this.$refs.box.rotationY = ry
          this.$refs.box.scale /= 1.5;
        }, 1 * this.$refs.box.animationStepTime)
      }
    },
    client_flip() {
      this.flip(
        this.$refs.box.rotationY == 180 ? 0 : 5
      )
    },

    updateParams(params) {
      if (!Array.isArray(params) || params.length < 4 ) {
        console.error("updateParams() extected 4 items in params array, less given")
        return
      }

      if (params[0]) this.$refs.box.positionX = params[0]
      if (params[1]) this.$refs.box.positionY = params[1]
      
      const orientation = params[2]
      if (orientation) this.flip(orientation)

      if (params[3] !== null) this.$refs.box.hidden = params[3]

      this.$refs.box.recentChanges = {}
    }
  }
}
</script>
