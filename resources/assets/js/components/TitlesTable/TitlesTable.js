import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

import { Table } from 'reactstrap';

class TitlesTable extends Component{


    constructor(props) {
        super(props);

        this.state = {
            titles: []
        };
    }

    componentDidMount() {
        this.loadContacts();
    }


    loadContacts() {
        const characterId = this.props.id;
        axios.get('/api/characters/' + characterId + '/titles')
            .then( res => {
                const titles = res.data;
                this.setState({
                    titles: titles
                });
            });
    }

    render() {
        return (
            <Table responsive>
                <thead>
                <tr>
                    <th>Title</th>
                </tr>
                </thead>
                <tbody>
                { this.state.titles.map(function (title) {
                    return (
                        <tr>
                            <td dangerouslySetInnerHTML={{__html: title.name}} />
                        </tr>
                    )
                })}
                </tbody>
            </Table>
        )
    }
}

TitlesTable.propTypes = {
    id: PropTypes.number
};

function mapStateToProps(state){
    return{
        user: state.user
    }
}

export default connect(mapStateToProps)(TitlesTable);