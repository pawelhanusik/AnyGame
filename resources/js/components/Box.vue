<template>
  <div
    :style="`
      position: absolute;
      left: ${positionX}px;
      top: ${positionY}px;
      width: ${width}px;
      height: ${height}px;
      visibility: ${hidden ? 'hidden' : 'visible'};
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
      <div class="d-flex justify-content-center flex-wrap align-content-center" :style="`background: ${front} ; background-size: cover; width: ${width}px    ; height: ${height}px   ; border: 2px solid ${ borderColor }; pointer-events: none; position: absolute; transform:                 translateZ(${thickness/2}px           )`" > {{textFront}} </div>
      <div class="d-flex justify-content-center flex-wrap align-content-center" :style="`background: ${top}   ; background-size: cover; width: ${width}px    ; height: ${thickness}px; border: 2px solid ${ borderColor }; pointer-events: none; position: absolute; transform: rotateX(90deg)  translateZ(${thickness/2}px           )`" > {{textTop}} </div>
      <div class="d-flex justify-content-center flex-wrap align-content-center" :style="`background: ${left}  ; background-size: cover; width: ${thickness}px; height: ${height}px   ; border: 2px solid ${ borderColor }; pointer-events: none; position: absolute; transform: rotateY(-90deg) translateZ(${thickness/2}px           )`" > {{textLeft}} </div>
      <div class="d-flex justify-content-center flex-wrap align-content-center" :style="`background: ${right} ; background-size: cover; width: ${thickness}px; height: ${height}px   ; border: 2px solid ${ borderColor }; pointer-events: none; position: absolute; transform: rotateY(90deg)  translateZ(${width - thickness/2}px   )`" > {{textRight}} </div>
      <div class="d-flex justify-content-center flex-wrap align-content-center" :style="`background: ${bottom}; background-size: cover; width: ${width}px    ; height: ${thickness}px; border: 2px solid ${ borderColor }; pointer-events: none; position: absolute; transform: rotateX(-90deg) translateZ(${height - thickness/2}px  )`" > {{textBottom}} </div>
      <div class="d-flex justify-content-center flex-wrap align-content-center" :style="`background: ${back}  ; background-size: cover; width: ${width}px    ; height: ${height}px   ; border: 2px solid ${ borderColor }; pointer-events: none; position: absolute; transform: rotateY(180deg) translateZ(${thickness/2}px           )`" > {{textBack}} </div>
    </div>
  </div>
</template>

<script>

let sendChangeIntervalID = null
const delayBetweenRequests = 200

export default {
  data() {
    return {
      scale: 1,
      rotationX: 0,
      rotationY: 0,
      rotationZ: 0,
      positionX: 500,
      positionY: 100,
      hidden: false,
      // -----------------
      isClicked: false,
      moveStartX: 0,
      moveStartY: 0,
      mouseDownTimestamp: 0,
      // -----------------
      recentChanges: {},
      lastSendTimestamp: 0,
      hasEditor: false,
      haveEditRights: false,
      shouldAbandonEditRights: false,
      haveOwnership: false
    }
  },
  props: {
    // IDENTITY
    componentID: {
      type: Number,
      required: true
    },
    gameID: {
      type: Number,
      required: true
    },
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
    isOnServerSide() {
      return (
        !this.haveOwnership
        && this.gameID
        && this.componentID
      )
    },
    canEdit() {
      return (
        this.haveEditRights
        || !this.isOnServerSide
      )
    },

    borderColor() {
      if (this.haveOwnership) {
        return 'yellow'
      }
      if (this.haveEditRights) {
        return 'green'
      }
      if (this.hasEditor) {
        return 'red'
      }
      return 'black'
    }
  },
  watch: {
    positionX(val) {
      this.sendChange('posX', val)
    },
    positionY(val) {
      this.sendChange('posY', val)
      if (val > document.body.offsetHeight / 2) {
        // HAND
        if (!this.haveOwnership) {
          this.askForOwnership()
        }
      } else {
        // TABLE
        if (this.haveOwnership) {
          this.abandonOwnership()
        }
      }
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

    sendChangeIntervalID = setInterval(() => {this.sendChange(null, null)}, delayBetweenRequests)
  },
  destroyed: function() {
    window.removeEventListener('mousemove', this.onMouseMove);
    window.removeEventListener('mousedown', this.onMouseDown);
    window.removeEventListener('mouseup', this.onMouseUp);

    if (sendChangeIntervalID !== null) {
      clearInterval(sendChangeIntervalID)
    }
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
        if (this.isOnServerSide) {
          // server event (if have rights)
          if (this.haveEditRights) {
            this.sendChange('event', 'action', true)
          }
        } else {
          // client event
          this.$emit('client_action')
        }
      }

      this.isClicked = false
      this.moveStartX = 0
      this.moveStartY = 0
    },
    onMouseLeave() {
      this.shouldAbandonEditRights = true

      this.isMouseOver = false
    },
    onMouseEnter() {
      this.shouldAbandonEditRights = false
      this.askForEditRights()

      this.isMouseOver = true
    },
    onMouseMove(evt) {
      if (
        this.isClicked
        && this.canEdit
      ) {
        this.positionX = evt.clientX - this.moveStartX
        this.positionY = evt.clientY - this.moveStartY
      }
    },
    // ---------------------------
    askForEditRights() {
      if (!this.isOnServerSide) {
        return
      }
      axios.get(`/${this.gameID}/components/${this.componentID}/editrights`).then((res) => {
        if (res?.data?.granted === true) {
          this.haveEditRights = true
          this.hasEditor = true
          this.shouldAbandonEditRights = false
        }
      }).catch((err) => {})
    },
    abandonEditRights() {
      if (!this.isOnServerSide) {
        return
      }
      this.haveEditRights = false
      this.hasEditor = false
      axios.delete(`/${this.gameID}/components/${this.componentID}/editrights`).then((res) => {
        if (res?.data?.abandoned === true) {
          this.shouldAbandonEditRights = false
        }
      }).catch((err) => {})
    },
    askForOwnership() {
      if (!this.isOnServerSide) {
        return
      }
      axios.get(`/${this.gameID}/components/${this.componentID}/ownership`).then((res) => {
        if (res?.data?.granted === true) {
          this.haveOwnership = true
        }
      }).catch((err) => {})
    },
    abandonOwnership() {
      this.haveOwnership = false
      axios.delete(`/${this.gameID}/components/${this.componentID}/ownership`).then((res) => {}).catch((err) => {})
    },
    sendChange(key, value, now = false) {
      if (this.haveOwnership) {
        this.recentChanges = {}
        return
      }
      if (!this.haveEditRights) {
        return
      }
      if (now) {
        let payload = {}
        payload[key] = value
        axios.put(`/${this.gameID}/components/${this.componentID}`, payload).then((res) => {
          if (typeof(res.data?.orientation) !== 'undefined') {
            this.$emit('action', res.data.orientation)
          }
        })
        return
      }
      if (Date.now() - this.lastSendTimestamp > delayBetweenRequests) {
        if (Object.keys(this.recentChanges).length > 0) {
          axios.put(`/${this.gameID}/components/${this.componentID}`, this.recentChanges).then((res) => {
            if (typeof(res.data?.orientation) !== 'undefined') {
              this.$emit('action', res.data.orientation)
            }
            
            this.recentChanges = {}
            if (this.shouldAbandonEditRights) {
              this.abandonEditRights()
            }
          })
        } else if (this.shouldAbandonEditRights) {
          this.abandonEditRights()
        }
        this.lastSendTimestamp = Date.now()
      } else if (key !== null && value !== null) {
        this.recentChanges[key] = value
      }
    }
  }
}
</script>
