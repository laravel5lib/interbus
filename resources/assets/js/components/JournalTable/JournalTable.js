import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

import { Table } from 'reactstrap';

import { Link } from 'react-router-dom';

class JournalTable extends Component{


    constructor(props) {
        super(props);

        this.state = {
            journal: []
        };
    }

    componentDidMount() {
        this.loadContacts();
    }


    loadContacts() {
        const characterId = this.props.id;
        axios.get('/api/characters/' + characterId + '/journal')
            .then( res => {
                const entries = _.orderBy(res.data, ['date'], ['desc']);
                this.setState({
                    journal: entries
                });
            });
    }

    render() {
        return (
            <Table responsive>
                <thead>
                <tr>
                    <th>Sender</th>
                    <th>Receiver</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                { this.state.journal.map(function (entry) {
                    return (
                        <tr>
                            <td>
                                <Link to={ ( entry.first_party_type === 'character' ? "/characters/" : '/corporation/') + entry.first_party_id}>
                                    { entry.first_party ? entry.first_party.name : entry.first_party_id }
                                </Link>
                            </td>
                            <td>
                                <Link to={ ( entry.second_party_type === 'character' ? "/characters/" : '/corporation/') + entry.second_party_id}>
                                    { entry.second_party ? entry.second_party.name : entry.second_party_id }
                                    </Link>
                            </td>
                            <td>
                                { Math.abs(entry.amount).toLocaleString() }
                            </td>
                            <td>
                                { entry.date }
                            </td>
                        </tr>
                    )
                })}
                </tbody>
            </Table>
        )
    }
}

JournalTable.propTypes = {
    id: PropTypes.number
};

function mapStateToProps(state){
    return{
        user: state.user
    }
}

export default connect(mapStateToProps)(JournalTable);