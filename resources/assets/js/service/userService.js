import axios from "axios/index";

export const userService = {
    login,
    logout,
    refresh
};

function login(email, password) {

    const requestOptions = {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
    };

    return fetch('/api/auth/login', requestOptions)
        .then(response => {
            if (!response.ok) {
                return Promise.reject(response.statusText);
            }

            return response.json();
        })
        .then(user => {
            // login successful if there's a jwt token in the response
            if (user && user.access_token) {
                // store user details and jwt token in local storage to keep user logged in between page refreshes
                localStorage.setItem('token', user.access_token)
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + user.access_token;
            }

            return user;
        });
}

function refresh(){


    return axios.post("/api/auth/refresh", {}, {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        }
    }).then(
            res => {
                if (res.status !== 200){
                    Promise.reject(res.statusText);
                }
                let user = res.data;
                if (user && user.access_token) {
                    // store user details and jwt token in local storage to keep user logged in between page refreshes
                    localStorage.setItem('token', user.access_token);
                    axios.defaults.headers.common['Authorization'] = 'Bearer ' + user.access_token;
                }
                return user;
            },
            err => {
                console.log(err);
                return err;
            }
        );
}

function logout() {
    // remove user from local storage to log user out
    localStorage.removeItem('user');
}
