// import Vue from 'vue';
// import App from './App.vue';

// new Vue({
//     el: '#sync-process-area',
//     render: h => h( App ),
// });

import { createApp } from 'vue';
import App from './App.vue';

// Create a Vue app instance and mount it to an element with id 'sync-process-area'
const app = createApp(App);
app.mount('#sync-process-area');
