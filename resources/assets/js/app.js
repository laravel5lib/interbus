//
// /**
//  * First we will load all of this project's JavaScript dependencies which
//  * includes React and other helpers. It's a great starting point while
//  * building robust, powerful web applications using React + Laravel.
//  */
//
// require('./bootstrap');
//
// /**
//  * Next, we will create a fresh React component instance and attach it to
//  * the page. Then, you may begin adding components to this application
//  * or customize the JavaScript scaffolding to fit your unique needs.
//  */
//
// require('./components/Example');


import React from 'react';

/* Import the Main component */
import ReactDOM from 'react-dom';
import {Router, Route, Switch} from 'react-router-dom';

// Styles
// Import Flag Icons Set
import 'flag-icon-css/css/flag-icon.min.css';
// Import Font Awesome Icons Set
import 'font-awesome/css/font-awesome.min.css';
// Import Simple Line Icons Set
import 'simple-line-icons/css/simple-line-icons.css';
// Import Main styles for this application
import '../../coreui/scss/style.scss'
// Temp fix for reactstrap
import '../../coreui/scss/core/_dropdown-menu-right.scss'

// Containers
import Full from './containers/Full'

import { SeatHome } from './containers/SeatHome/SeatHome';

import { store } from "./store/store";
import { Provider } from "react-redux";
import { PrivateRoute } from './components/PrivateRoute';

// Views
import { Login } from './views/Pages/Login'
import Register from './views/Pages/Register/'
import Page404 from './views/Pages/Page404/'
import Page500 from './views/Pages/Page500/'

import { history } from './helpers/history';

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

ReactDOM.render((
    <Provider store={store}>
        <Router history={history}>
            <Switch>
                <Route exact path="/login" name="Login Page" component={Login}/>
                <Route exact path="/register" name="Register Page" component={Register}/>
                <Route exact path="/404" name="Page 404" component={Page404}/>
                <Route exact path="/500" name="Page 500" component={Page500}/>
                <PrivateRoute path="/" name="Home" component={SeatHome}/>
            </Switch>
        </Router>
    </Provider>
), document.getElementById('root'));
