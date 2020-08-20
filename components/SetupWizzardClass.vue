  
<template>
    <b-input-group class="mt-3">
        <b-form-input placeholder="Class name" type="text" v-model="name" @change="update()" />
        <b-btn variant="danger" @click="remove()">X</b-btn>
    </b-input-group>
</template>
<script>
export default {
  name: "SetupWizzardClass",
  props: {
      index: {
          type: Number
      },
      className: {
          type: String,
          default: ""
      }
  },
  data() {
      return {
          name: this.className
      }
  },
  methods: {
      update() {
          $nuxt.$emit("setup-class-update", {index: this.index, name: this.name})
      },
      remove() {
          $nuxt.$emit("setup-class-remove", this.index)
      }
  }
};
</script>