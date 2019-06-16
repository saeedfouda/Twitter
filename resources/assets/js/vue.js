window.Vue = require('vue');
import VeeValidation from 'vee-validate';

Vue.use(VeeValidation);

import { Validator } from 'vee-validate';

let instance = new Validator();

function isUnique(value, uniqueTo){
    return axios.post('/checkUnique', {
        value: value,
        uniqueTo: uniqueTo
    }).then(resp => {
        return true;
    }).catch(error => {
        return false;
    });
}

// Also there is an instance 'extend' method for convenience.
instance.extend('unique', {
    getMessage: field => 'Please pick up another ' + field + '. This one is already used.',
    validate: (value, [uniqueTo]) => isUnique(value, uniqueTo)
});

const dictionary = {
  en: {
    attributes: {
      current_password: 'password',
      new_password: 'new password',
      password_confirmation: 'password'
    }
  }
};

Validator.localize(dictionary);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example-component', require('./components/ExampleComponent.vue'));
//
// const app = new Vue({
//     el: '#app'
// });
