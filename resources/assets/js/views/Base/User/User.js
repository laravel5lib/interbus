import React, { Component } from 'react';

class User extends Component {
    constructor(props) {
        super(props);
    }

    componentDidMount = () => {
        const csrf = window.Laravel.csrfToken;
        axios.get('/api/auth/user', {
            headers: {
                'X-CSRF-TOKEN' : csrf
            }
        })
            .then(user => {
                console.log(user);
            })
    }

    render() {
        return (
            <div className="animated fadeIn">

            </div>
        )
    }
}

export default User;