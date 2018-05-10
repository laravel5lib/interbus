import { userConstants } from '../constants';
import jwt from 'jsonwebtoken';
import axios from 'axios';

function getInitialState(){

    const token = localStorage.getItem('token');

    if (token) {
        return {
            loggingIn: false,
            name: 'Sumo',
            email: 'fireddarksumoh@gmail.com',
            token: token
        }
    }

    return {
        loggingIn: false,
        name: null,
        email: null,
        token: null,
    }
}

const initialState = getInitialState();

export function user(state = initialState, action) {
    switch (action.type) {
        case userConstants.REFRESH_REQUEST:
            return {
                ...state,
                loggingIn: true
            };
        case userConstants.REFRESH_SUCCESS:
            return{
                ...state,
                loggingIn: false,
            };

        case userConstants.REFRESH_FAILURE:
            return {
                ...state,
                loggedIn: false,
                email: null,
                name: null,
                expires: null,
                token: null,
            }

        case userConstants.LOGIN_REQUEST:
            return {
                ...state,
                loggingIn: true,
            };

        case userConstants.LOGIN_SUCCESS:
            console.log(action);
            console.log(action.user);
            return {
                ...state,
                loggingIn: false,
            };
        case userConstants.LOGIN_FAILURE:
            return {
                ...state,
                loggingIn: false
            };
        case userConstants.LOGOUT:
            return {
                ...state,
                loggingIn: false,
                name: null,
                email: null,
                expires: null,
                token: null,
            };
        default:
            return state
    }

}