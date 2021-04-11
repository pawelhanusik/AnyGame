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

    :initialRotationX="rotX"
    :initialRotationY="rotY"
    :initialRotationZ="rotZ"
    :posX="posX"
    :posY="posY"

    :animationStepTime="300"

    @action="roll"
    @client_action="client_roll"
  />
</template>

<script>
import Box from '@/components/Box.vue'

const rotationFromOrientationTable = [
  [
    [0, 0, 0],
    [0, 0, 1],
    [0, 0, 2],
    [0, 0, 3],
    [2, 2, 0],
    [2, 2, 1],
    [2, 2, 2],
    [2, 2, 3]
  ], [
    [0, 1, 3],
    [0, 3, 1],
    [1, 0, 2],
    [1, 1, 2],
    [1, 2, 2],
    [1, 3, 2],
    [2, 1, 1],
    [2, 3, 3],
    [3, 0, 0],
    [3, 1, 0],
    [3, 2, 0],
    [3, 3, 0]
  ], [
    [0, 1, 0],
    [0, 3, 2],
    [1, 0, 3],
    [1, 1, 3],
    [1, 2, 3],
    [1, 3, 3],
    [2, 1, 2],
    [2, 3, 0],
    [3, 0, 1],
    [3, 1, 1],
    [3, 2, 1],
    [3, 3, 1]
  ], [
    [0, 1, 2],
    [0, 3, 0],
    [1, 0, 1],
    [1, 1, 1],
    [1, 2, 1],
    [1, 3, 1],
    [2, 1, 0],
    [2, 3, 2],
    [3, 0, 3],
    [3, 1, 3],
    [3, 2, 3],
    [3, 3, 3]
  ], [
    [0, 1, 1],
    [0, 3, 3],
    [1, 0, 0],
    [1, 1, 0],
    [1, 2, 0],
    [1, 3, 0],
    [2, 1, 3],
    [2, 3, 1],
    [3, 0, 2],
    [3, 1, 2],
    [3, 2, 2],
    [3, 3, 2]
  ], [
    [0, 2, 0],
    [0, 2, 1],
    [0, 2, 2],
    [0, 2, 3],
    [2, 0, 0],
    [2, 0, 1],
    [2, 0, 2],
    [2, 0, 3]
  ]
]

export default {
  components: {
    Box
  },
  data() {
    return {
      animationIsRunning: false,
      animationQueue: []
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

    size: {
      type: Number,
      default: 64
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
  methods: {
    // actions
    roll(orientation) {
      //console.log("orientation", orientation)
      const allPosiibleRotations = rotationFromOrientationTable[orientation]
      let [rx, ry, rz] = allPosiibleRotations[
        Math.floor(Math.random() * allPosiibleRotations.length)
      ]
      //console.log("DICE roti: ", rx, ry, rz)
      /*if (rx == 0) rx = 4
      if (ry == 0) ry = 4
      if (rz == 0) rz = 4*/
      rx *= 90
      ry *= 90
      rz *= 90
      /*
      console.log("DICE rotations: ", rx, ry, rz)
      console.log("DICE current: ", this.$refs.box.rotationX, this.$refs.box.rotationY, this.$refs.box.rotationZ)
      rx = rx - this.$refs.box.rotationX % 360
      ry = rx - this.$refs.box.rotationY % 360
      rz = rx - this.$refs.box.rotationZ % 360
      console.log("DICE rotations after tweak: ", rx, ry, rz)
      */

      this.animationAddStep(() => {
        this.$refs.box.rotationX = rx/2
        this.$refs.box.rotationY = ry/2
        this.$refs.box.rotationZ = rz/2
        this.$refs.box.scale *= 1.5;
      })
      this.animationAddStep(() => {
        this.$refs.box.rotationX = rx
        this.$refs.box.rotationY = ry
        this.$refs.box.rotationZ = rz
        this.$refs.box.scale /= 1.5;
      })
      this.animationStart()
    },
    client_roll() {
      this.roll(
        Math.floor(Math.random() * 6)
      )
    },

    animationStep() {
      if (this.animationQueue.length <= 0) {
        this.animationIsRunning = false
        return
      }
      const stepFunc = this.animationQueue.shift()
      stepFunc()
      
      setTimeout(this.animationStep, this.$refs.box.animationStepTime)
    },
    animationStart() {
      if (this.animationIsRunning) {
        // already started
        return
      }
      this.animationIsRunning = true
      this.animationStep()
    },
    animationAddStep(func) {
      this.animationQueue.push(func)
    },

    updateParams(params) {
      if (!Array.isArray(params) || params.length < 4 ) {
        console.error("updateParams() extected 4 items in params array, less given")
        return
      }

      if (params[0]) this.$refs.box.positionX = params[0]
      if (params[1]) this.$refs.box.positionY = params[1]
      
      const orientation = params[2]
      if (orientation) this.roll(orientation)

      if (params[3] !== null) this.$refs.box.hidden = params[3]

      this.$refs.box.recentChanges = {}
    }
  }
}
</script>
