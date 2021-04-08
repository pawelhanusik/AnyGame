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

  </div>
</template>

<script>
export default {
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
  }
}
</script>
