import { userConstants } from '../constants';
import jwt from 'jsonwebtoken';
import axios from 'axios';

function getInitialState(){

    const token = localStorage.getItem('token');

    if (token) {
        const decoded = jwt.decode(token);
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
        return {
            loggingIn: false,
            name: decoded.name,
            email: decoded.email,
            expires: decoded.exp,
            token: token
        }
    }

    return {
        loggingIn: false,
        name: null,
        email: null,
        expires: null,
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
            let rtoken = jwt.decode(action.user.access_token);
            return{
                ...state,
                loggingIn: false,
                email: rtoken.email,
                name: rtoken.name,
                expires: rtoken.exp,
                token: action.user.access_token,
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
            let token = jwt.decode(action.user.access_token);
            return {
                ...state,
                loggingIn: false,
                email: token.email,
                name: token.name,
                expires: token.exp,
                token: action.user.access_token,
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