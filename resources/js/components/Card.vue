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

    updateParams(posX = null, posY = null, orientation = null, isHidden = null, isOwner = null, isEditor = null, hasEditor = null) {
      if (posX !== null) this.$refs.box.positionX = posX
      if (posY !== null) this.$refs.box.positionY = posY
      if (orientation !== null) this.flip(orientation)
      if (isHidden !== null) this.$refs.box.hidden = isHidden
      if (isOwner !== null) this.$refs.box.haveOwnership = isOwner
      if (isEditor !== null) this.$refs.box.haveEditRights = isEditor
      if (hasEditor !== null) this.$refs.box.hasEditor = hasEditor

      this.$refs.box.recentChanges = {}
    }
  }
}
</script>
