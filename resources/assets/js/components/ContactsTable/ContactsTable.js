import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

import { Link } from 'react-router-dom';

import { Table, Col } from 'reactstrap';
import { RingLoader } from 'react-spinners';
import {history} from '../../helpers/history';

class ContactsTable extends Component{

    constructor(props) {
        super(props);

        this.state = {
            contacts: [],
            loading: true
        };
    }

    componentDidMount() {
        this.loadContacts();
    }

    loadContacts() {
        const characterId = this.props.id;
        axios.get('/api/characters/' + characterId + '/contacts')
            .then( res => {
               const contacts = _.orderBy(res.data, ['standing', 'contact.name'], ['desc', 'asc']);
               this.setState({
                   contacts: contacts,
                   loading: false
               });
            });
    }

    redirect = (event, type, id) => {
        event.preventDefault();
        history.push('/' + type + 's/' +id);
    }

    render() {
        return (
            <Table responsive>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Standing</th>
                </tr>
                </thead>
                <tbody>
                {this.state.contacts.map(function (contact) {
                    return (
                        <tr>
                            <td>
                                <Link to={'/' + contact.contact_type + 's/' + contact.contact_id} >{contact.contact.name}</Link>
                            </td>
                            <td>
                                {contact.standing}
                            </td>
                        </tr>
                    )
                }, this)}
                </tbody>

                <RingLoader
                    color={'#123abc'}
                    loading={this.state.loading}
                />

            </Table>
        )
    }
}

ContactsTable.propTypes = {
    id: PropTypes.number
};

function mapStateToProps(state){
    return{
        user: state.user
    }
}

export default connect(mapStateToProps)(ContactsTable);