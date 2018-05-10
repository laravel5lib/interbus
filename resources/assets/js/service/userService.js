import axios from "axios/index";

export const userService = {
    login,
    logout,
    refresh
};

function login(email, password) {

    return axios.post('/api/auth/login',
        {
            username: email,
            password: password,
        })
        .then(token => {
            console.log(token);
            localStorage.setItem('token', token);
            return token;
        });
}

function refresh(){


    return axios.post("/oauth/token", {}, {
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
