<template>
  <div
    :style="`
      position: absolute;
      left: ${positionX}px;
      top: ${positionY}px;
      width: ${width}px;
      height: ${height}px;
      
    `"
    @mouseleave="onMouseLeave"
    @mouseenter="onMouseEnter"
  >
    <div
      :style="`
        width: ${width}px;
        height: ${height}px;  
        transform-style: preserve-3d;
        transform: \
          scale(${scale}) \
          rotateX(${rotationX}deg) \
          rotateY(${rotationY}deg) \
          rotateZ(${rotationZ}deg) \
        ;
        transition: all linear ${animationStepTime}ms;
      `"
    >
      <div class="d-flex justify-content-center flex-wrap align-content-center" :style="`background: ${front} ; background-size: cover; width: ${width}px    ; height: ${height}px   ; border: 1px solid black; pointer-events: none; position: absolute; transform:                 translateZ(${thickness/2}px           )`" > {{textFront}} </div>
      <div class="d-flex justify-content-center flex-wrap align-content-center" :style="`background: ${top}   ; background-size: cover; width: ${width}px    ; height: ${thickness}px; border: 1px solid black; pointer-events: none; position: absolute; transform: rotateX(90deg)  translateZ(${thickness/2}px           )`" > {{textTop}} </div>
      <div class="d-flex justify-content-center flex-wrap align-content-center" :style="`background: ${left}  ; background-size: cover; width: ${thickness}px; height: ${height}px   ; border: 1px solid black; pointer-events: none; position: absolute; transform: rotateY(-90deg) translateZ(${thickness/2}px           )`" > {{textLeft}} </div>
      <div class="d-flex justify-content-center flex-wrap align-content-center" :style="`background: ${right} ; background-size: cover; width: ${thickness}px; height: ${height}px   ; border: 1px solid black; pointer-events: none; position: absolute; transform: rotateY(90deg)  translateZ(${width - thickness/2}px   )`" > {{textRight}} </div>
      <div class="d-flex justify-content-center flex-wrap align-content-center" :style="`background: ${bottom}; background-size: cover; width: ${width}px    ; height: ${thickness}px; border: 1px solid black; pointer-events: none; position: absolute; transform: rotateX(-90deg) translateZ(${height - thickness/2}px  )`" > {{textBottom}} </div>
      <div class="d-flex justify-content-center flex-wrap align-content-center" :style="`background: ${back}  ; background-size: cover; width: ${width}px    ; height: ${height}px   ; border: 1px solid black; pointer-events: none; position: absolute; transform: rotateY(180deg) translateZ(${thickness/2}px           )`" > {{textBack}} </div>
    </div>
  </div>
</template>

<script>
const magicArray = [
  [
    [1, 1, 1, 1],
    [3, 5, 4, 2],
    [6, 6, 6, 6],
    [4, 2, 3, 5]
  ],
  [
    [5, 4, 2, 3],
    [5, 4, 2, 3],
    [5, 4, 2, 3],
    [5, 4, 2, 3]
  ],
  [
    [6, 6, 6, 6],
    [4, 2, 3, 5],
    [1, 1, 1, 1],
    [3, 5, 4, 2]
  ],
  [
    [2, 3, 5, 4],
    [2, 3, 5, 4],
    [2, 3, 5, 4],
    [2, 3, 5, 4]
  ]
]
export default {
  data() {
    return {
      scale: 1,
      rotationX: 0,
      rotationY: 0,
      rotationZ: 0,
      positionX: 500,
      positionY: 100,
      // -----------------
      isClicked: false,
      moveStartX: 0,
      moveStartY: 0,
      mouseDownTimestamp: 0
    }
  },
  props: {
    // DIMENSIONS
    width: {
      type: Number,
      default: 64
    },
    height: {
      type: Number,
      default: 128
    },
    thickness: {
      type: Number,
      default: 16
    },

    // BACKGROUND
    front: {
      type: String,
      default: "#afafaf"
    },
    top: {
      type: String,
      default: "#afafaf"
    },
    left: {
      type: String,
      default: "#afafaf"
    },
    right: {
      type: String,
      default: "#afafaf"
    },
    bottom: {
      type: String,
      default: "#afafaf"
    },
    back: {
      type: String,
      default: "#afafaf"
    },
    // INITIAL PROPS
    initialRotationX: {
      type: Number,
      default: 0
    },
    initialRotationY: {
      type: Number,
      default: 0
    },
    initialRotationZ: {
      type: Number,
      default: 0
    },
    posX: {
      type: Number,
      default: 500
    },
    posY: {
      type: Number,
      default: 100
    },
    // TEXT
    textFront: {
      type: String,
      default: ""
    },
    textTop: {
      type: String,
      default: ""
    },
    textLeft: {
      type: String,
      default: ""
    },
    textRight: {
      type: String,
      default: ""
    },
    textBottom: {
      type: String,
      default: ""
    },
    textBack: {
      type: String,
      default: ""
    },
    // ADDITIONAL PROPS
    animationStepTime : {
      type: Number,
      default: 200
    }
  },
  computed: {
    visibleSide() {
      const rx = Math.round((this.rotationX % 360) / 90) % 4
      const ry = Math.round((this.rotationY % 360) / 90) % 4
      const rz = Math.round((this.rotationZ % 360) / 90) % 4

      return magicArray[rx][ry][rz]
    }
  },

  created: function() {
    window.addEventListener('mousemove', this.onMouseMove);
    window.addEventListener('mousedown', this.onMouseDown);
    window.addEventListener('mouseup', this.onMouseUp);

    this.rotationX = this.initialRotationX
    this.rotationY = this.initialRotationY
    this.rotationZ = this.initialRotationZ
    this.positionX = this.posX
    this.positionY = this.posY
  },
  destroyed: function() {
    window.removeEventListener('mousemove', this.onMouseMove);
    window.removeEventListener('mousedown', this.onMouseDown);
    window.removeEventListener('mouseup', this.onMouseUp);
  },

  methods: {
    // events
    onMouseDown(evt) {
      if (this.isMouseOver) {
        this.isClicked = true
        this.moveStartX = evt.clientX - this.positionX
        this.moveStartY = evt.clientY - this.positionY
        this.mouseDownTimestamp = Date.now()
      }
    },
    onMouseUp() {
      if (
        this.isClicked
        && Date.now() - this.mouseDownTimestamp < 100
      ) {
        //this.flip()
        this.$emit('action')
      }

      this.isClicked = false
      this.moveStartX = 0
      this.moveStartY = 0
    },
    onMouseLeave() {
      this.isMouseOver = false
    },
    onMouseEnter() {
      this.isMouseOver = true
    },
    onMouseMove(evt) {
      if (this.isClicked) {
        this.positionX = evt.clientX - this.moveStartX
        this.positionY = evt.clientY - this.moveStartY
      }
    }
    // ---------------------------
  }
}
</script>
