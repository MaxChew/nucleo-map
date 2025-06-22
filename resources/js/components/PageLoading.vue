<template>
    <div class="page-loading bg-bg-revert"></div>
  </template>
  
  <script>
  export default {
    mounted() {
        window.addEventListener("beforeunload", this.showLoadingAnimationBeforeUnload);
        window.addEventListener("load", this.showLoadingAnimationOnLoad);
    },
  
    methods: {
        showLoadingAnimationBeforeUnload(event) {
          document.querySelector(".page-loading").classList.add("show-before-unload");
          document.querySelector(".page-loading").classList.remove("show-on-load");
        },
        showLoadingAnimationOnLoad() {
          document.querySelector(".page-loading").classList.remove("show-before-unload");
          document.querySelector(".page-loading").classList.add("show-on-load");
          setTimeout(() => {
              this.hideLoadingAnimation();
          }, 500);
        },
        hideLoadingAnimation() {
          document.querySelector(".page-loading").classList.remove("show-on-load");
        }
    }
  }
  </script>
  
  <style>
  .page-loading {
    content: " ";
    display: none;
    position: fixed;
    z-index: 1000000;
    height: 4px;
    width: 100%;
    top: 0;
    left: 0;
    box-shadow: 0 2px 2px rgba(0, 0, 0, .2);
    animation: none;
  }
  
  .page-loading.show-before-unload {
    display: block;
    animation: page-load-before-unload 3.5s linear forwards;
  }
  
  .page-loading.show-on-load {
    display: block;
    animation: page-load-on-load 0.2s linear forwards;
  }
  
  @keyframes page-load-before-unload {
      0% {
        width: 0%; 
      }
      100% {
        width: 70%; 
      }
  }
  
  @keyframes page-load-on-load {
      0% {
        width: 70%; 
      }
      100% {
        width: 100%; 
      }
  }
  </style>
  