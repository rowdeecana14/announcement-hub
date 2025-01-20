// FILE: main.js

import { createApp } from 'vue'
import { Quasar, Dialog, AppFullscreen, Loading, Notify } from "quasar";

// Import icon libraries
import '@quasar/extras/material-icons/material-icons.css'

// Import Quasar css
import "./style.css";
import "quasar/src/css/index.sass";
import "sweetalert2/dist/sweetalert2.min.css";

// Assumes your root component is App.vue
// and placed in same folder as main.js
import app from '@app/src/App.vue';
import router from "@app/src/router";
import { runInterceptors } from "@utils/services/Axios.js";

runInterceptors();
const myApp = createApp(app)

myApp.use(router);
myApp.use(Quasar, {
  plugins: {
    AppFullscreen,
    Dialog,
    Loading,
    Notify,
  }, // import Quasar plugins and add here
})

// Assumes you have a <div id="app"></div> in your index.html
myApp.mount('#app')
