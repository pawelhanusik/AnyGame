<template>
  <div>
    <card
      v-for="(c, index) in cardsInDeck"
      :key="index"

      :suit="c.suit"
      :rank="c.rank"
      :startReversed="startReversed"

      :posX="posX"
      :posY="posY"
    />
  </div>
</template>

<script>
import Card from '@/components/Card.vue'

let cardsInDeck = []
for (let suit of ['spades', 'hearths', 'diamonds', 'clubs']) {
  for (let rank of ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A']) {
    cardsInDeck.push({
      suit,
      rank
    })
  }
}

function shuffleArray(array) {
    var j, x, i;
    for (i = array.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        x = array[i];
        array[i] = array[j];
        array[j] = x;
    }
    return array;
}


export default {
  components: {
    Card
  },
  data() {
    return {
      cardsInDeck: cardsInDeck
    }
  },
  props: {
    shuffle: {
      type: Boolean,
      default: true
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
  created() {
    if (this.shuffle) {
      this.cardsInDeck = shuffleArray(cardsInDeck)
    }
  }
}
</script>
