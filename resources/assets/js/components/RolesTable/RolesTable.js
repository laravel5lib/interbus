import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

import { Table } from 'reactstrap';

class RolesTable extends Component{


    constructor(props) {
        super(props);

        this.state = {
            roles: []
        };
    }

    componentDidMount() {
        this.loadContacts();
    }


    loadContacts() {
        const characterId = this.props.id;
        axios.get('/api/characters/' + characterId + '/roles')
            .then( res => {
                const roles = _.orderBy(res.data, ['location', 'role'], ['desc', 'asc']);
                this.setState({
                    roles: roles
                });
            });
    }

    render() {
        return (
            <Table responsive>
                <thead>
                <tr>
                    <th>Role</th>
                    <th>Location</th>
                </tr>
                </thead>
                <tbody>
                { this.state.roles.map(function (role) {
                    return (
                        <tr>
                            <td>{role.role.split('_').join(' ')}</td>
                            <td>{role.location ? role.location : 'General'}</td>
                        </tr>
                    )
                })}
                </tbody>
            </Table>
        )
    }
}

RolesTable.propTypes = {
    id: PropTypes.number
};

function mapStateToProps(state){
    return{
        user: state.user
    }
}

export default connect(mapStateToProps)(RolesTable);