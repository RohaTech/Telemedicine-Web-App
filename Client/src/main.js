import "./assets/main.css";
import "flatpickr/dist/flatpickr.css";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import { createApp } from "vue";
import { createPinia } from "pinia";

import App from "./App.vue";
import router from "./router";

const app = createApp(App);

const options = {
  position: "top-right",
  timeout: 2000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: "button",
  icon: true,
  rtl: false,

};

app.use(createPinia());
app.use(router);
app.use(Toast, options);
app.mount("#app");