import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

import { Table } from 'reactstrap';

class MailTable extends Component{


    constructor(props) {
        super(props);

        this.state = {
            mail: []
        };
    }

    componentDidMount() {
        this.loadContacts();
    }


    loadContacts() {
        const characterId = this.props.id;
        axios.get('/api/characters/' + characterId + '/mail')
            .then( res => {
                const mail = _.orderBy(res.data, 'timestamp', 'desc');
                this.setState({
                    mail: mail
                });
            });
    }

    render() {
        return (
            <Table responsive>
                <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                { this.state.mail.map(function (mail) {
                    return (
                        <tr>
                            <td>{ mail.sender ? mail.sender.name : 'Unknown' }</td>
                            <td>{ mail.subject }</td>
                            <td>{ mail.timestamp }</td>
                        </tr>
                    )
                })}
                </tbody>
            </Table>
        )
    }
}

MailTable.propTypes = {
    id: PropTypes.number
};

function mapStateToProps(state){
    return{
        user: state.user
    }
}

export default connect(mapStateToProps)(MailTable);