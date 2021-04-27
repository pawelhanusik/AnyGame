<template>
  <div>
    <div class="row">
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
      type: Array,
      default: () => {
        return []
      }
    }
  },
  created() {
    axios.get(`/${this.game.id}/components`).then((res) => {
      this.createComponents(res.data.data)
    })
    Echo.join(`game-channel.${this.game.id}`)
      .here((users) => {
        for (let u of users) {
          this.addPlayer(u)
        }
      })
      .joining((user) => {
        this.addPlayer(user)
      })
      .leaving((user) => {
        this.removePlayer(user)
      })
      .listen('GameComponentUpdateEvent', (e) => {
        const updatedValues = e.updatedValues
        const component2update = this.gameComponents[e.componentID]
        if (!component2update) {
          axios.get(`/${this.game.id}/components/${e.componentID}`).then((res) => {
            this.createComponents([res.data.data])
          })
          return
        }
        
        let isVisible = null
        if (typeof(updatedValues['visibility']) != 'undefined' && updatedValues['visibility'] !== null) {
          isVisible = (updatedValues['visibility'] === 'hidden')
        }
        component2update.updateParams(
          updatedValues['posX'] ?? null,
          updatedValues['posY'] ?? null,
          updatedValues['orientation'] ?? null,
          isVisible,
          null,
          null,
          updatedValues['hasEditor'] ?? null
        )
      })
  },
  destroyed() {
    Echo.leave()
  },
  methods: {
    addPlayer(player) {
      for (let p of this.players) {
        if (p.id == player.id) {
          return
        }
      }
      this.players.push(player)
    },
    removePlayer(player) {
      for (let p of this.players) {
        if (p.id == player.id) {
          this.players.splice(this.players.indexOf(p), 1)
        }
      }
    },
    createComponents(components) {
      const gameComponentsContainer = this.$refs['game_components_container']

      let lastAddedHandComponentPosX = 100;
      let lastAddedHandComponentPosY = document.body.offsetHeight / 2 + 100;
      for (let c of components) {
        let newComponent = null
        switch(c.component_type) {
          case 'App\\Models\\Dice':
            newComponent = new DiceClass({
              propsData: {
                componentID: parseInt(c.id),
                gameID: parseInt(c.game_id),
                posX: parseInt(c.posX),
                posY: parseInt(c.posY)
              }
            })
            break
          case 'App\\Models\\Card':
            newComponent = new CardClass({
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
            break
        }
        if (newComponent === null) {
          continue
        }
        newComponent.$mount()
        gameComponentsContainer.appendChild(newComponent.$el)

        if (c.is_owner) {
          lastAddedHandComponentPosX += 10
          if (lastAddedHandComponentPosX > 500) {
            lastAddedHandComponentPosX = 100
            lastAddedHandComponentPosY += 10
          }
        }
        let isVisible = null
        if (typeof(c.visibility) != 'undefined' && c.visibility !== null) {
          isVisible = (c.visibility === 'hidden')
        }
        newComponent.updateParams(
          c.is_owner ? lastAddedHandComponentPosX : null,
          c.is_owner ? lastAddedHandComponentPosY : null,
          c.orientation ?? null,
          isVisible,
          c.is_owner ?? null,
          c.is_editor ?? null,
          c.has_editor ?? null
        )
        
        this.gameComponents[c.id] = newComponent
      }
    }
  }
}
</script>
