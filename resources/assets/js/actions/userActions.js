import { userConstants } from '../constants';
import { userService } from '../service/userService';

export const userActions = {
    login,
    logout,
    refresh
};

import { history } from '../helpers/history';

function login(email, password) {

    return dispatch => {

        dispatch(request());

        userService.login(email, password)
            .then(
                user => {
                    dispatch(success(user));
                    history.push('/');
                },
                error => {
                    dispatch(failure(error));
                }
            );
    };

    function request() {
        return { type: userConstants.LOGIN_REQUEST }
    }

    function success(user) {
        return { type: userConstants.LOGIN_SUCCESS, user }
    }

    function failure(error) {
        return { type: userConstants.LOGIN_FAILURE, error }
    }
}

function refresh() {

    return dispatch => {
        dispatch(request());

        userService.refresh()
            .then(
                user => {
                    dispatch(success(user));
                },
                error => {
                    dispatch(failure(error));
                }
            );
    }

    function request() {
        return { type: userConstants.REFRESH_REQUEST }
    }

    function success(user) {
        return { type: userConstants.REFRESH_SUCCESS, user }
    }

    function failure(error) {
        return { type: userConstants.REFRESH_FAILURE, error }
    }
}

function logout() {
    userService.logout();
    history.push("/login");
    return {type: userConstants.LOGOUT};
}