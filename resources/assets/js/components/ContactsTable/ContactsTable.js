import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

import { Table, Col } from 'reactstrap';
import { RingLoader } from 'react-spinners';

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
               const contacts = res.data;
               this.setState({
                   contacts: contacts,
                   loading: false
               });
            });
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
                                <a href={'/' + contact.contact_type + 's/' + contact.contact_id}>{contact.contact.name}</a>
                            </td>
                            <td>
                                {contact.standing}
                            </td>
                        </tr>
                    )
                })}
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