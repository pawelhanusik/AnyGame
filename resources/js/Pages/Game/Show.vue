<template>
  <div>
    <div class="row">
      <div class="col-md-2" style="background: #cccccc; border-right: 1px solid black; min-height: 100vh;">
        <!-- STATUS -->
        <h2 class="text-center mt-3"> {{ game.name }} </h2>
        <hr/>
        <h4 class="ml-3"> Players: </h4>
        <ul>
          <li v-for="player in players" :key="player.id" > {{player.nick}} </li>
        </ul>
        <!-- ====== -->
      </div>
      <div class="col-md-10">
        <div class="row h-100 flex-column">
          <div class="col-md-6 mw-100" style="background: #efefef; border-bottom: 1px solid black;">
            <!-- TABLE -->
          </div>
          <div class="col-md-6 mw-100" style="background: #e5e5e5;">
            <!-- HAND -->
          </div>
        </div>
      </div>
    </div>

    <div ref="game_components_container" />
  </div>
</template>

<script>
import Vue from 'vue'
import Dice from '@/components/Dice.vue'
import Card from '@/components/Card.vue'

const DiceClass = Vue.extend(Dice)
const CardClass = Vue.extend(Card)

export default {
  components: {
    Dice,
    Card
  },
  data() {
    return {
      gameComponents: {}
    }
  },
  props: {
    nick: {
      type: String
    },
    game: {
      type: Object
    },
    players: {
      type: Array
    }
  },
  created() {
    axios.get(`/${this.game.id}/components`).then((res) => {
      this.createComponents(res.data.data)
    })
    Echo.join(`game-channel.${this.game.id}`)
      .listen('GameComponentUpdateEvent', (e) => {
        const updatedValues = e.updatedValues
        const component2update = this.gameComponents[e.componentID]
        if (!component2update) {
          console.error("Cannot find component to update")
          return
        }

        component2update.updateParams([
          parseInt(updatedValues['posX']) ?? null,
          parseInt(updatedValues['posY']) ?? null,
          parseInt(updatedValues['orientation']) ?? null,
          (updatedValues['visibility'] === 'hidden')
        ])
      })
  },
  destroyed() {
    Echo.leave()
  },
  methods: {
    createComponents(components) {
      const gameComponentsContainer = this.$refs['game_components_container']

      for (let c of components) {
        switch(c.component_type) {
          case 'App\\Models\\Dice':
            const newDice = new DiceClass({
              propsData: {
                componentID: parseInt(c.id),
                gameID: parseInt(c.game_id),
                posX: parseInt(c.posX),
                posY: parseInt(c.posY)
              }
            })
            newDice.$mount()
            gameComponentsContainer.appendChild(newDice.$el)

            newDice.updateParams([null, null, parseInt(c.orientation) ?? null, null])
            
            this.gameComponents[c.id] = newDice
            break
          case 'App\\Models\\Card':
            const newCard = new CardClass({
              propsData: {
                componentID: parseInt(c.id),
                gameID: parseInt(c.game_id),
                
                posX: parseInt(c.posX),
                posY: parseInt(c.posY),

                suit: c.component.suit,
                rank: c.component.rank,
                startReversed: (c.orientation != 0)
              }
            })
            newCard.$mount()
            gameComponentsContainer.appendChild(newCard.$el)
            
            this.gameComponents[c.id] = newCard
            break
        }
      }
    }
  }
}
</script>
