import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

import { Table, Col } from 'reactstrap';

import { Link } from 'react-router-dom';

class ChatChannelsTable extends Component{

    constructor(props) {
        super(props);

        this.state = {
            channels: [],
        };
    }

    componentDidMount() {
        this.loadContacts();
    }

    loadContacts() {
        const characterId = this.props.id;
        axios.get('/api/characters/' + characterId + '/chat')
            .then( res => {
                const channels = _.orderBy(res.data, ['standing', 'contact.name'], ['desc', 'asc']);
                this.setState({
                    channels: channels
                });
            });
    }

    render() {
        return (
            <Table responsive>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Owner</th>
                </tr>
                </thead>
                <tbody>
                {this.state.channels.map(function (channel) {
                    return (
                        <tr>
                            <td>
                                <Link to={'/chat/' + channel.channel_id}>{channel.name}</Link>
                            </td>
                            <td>
                                <Link to={'/characters/' + channel.owner.character_id}>{channel.owner.name}</Link>
                            </td>
                        </tr>
                    )
                }, this)}
                </tbody>
            </Table>
        )
    }
}

ChatChannelsTable.propTypes = {
    id: PropTypes.number
};

function mapStateToProps(state){
    return{
        user: state.user
    }
}

export default connect(mapStateToProps)(ChatChannelsTable);