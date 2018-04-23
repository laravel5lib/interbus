import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

import { Table, Col, Card } from 'reactstrap';

import { Link } from 'react-router-dom';
import axios from "axios/index";

class CharacterSheetTable extends Component{

    constructor(props) {
        super(props);

        this.state = {
            online: [],
            fatigue: [],

        };
    }

    componentDidMount() {
        this.loadCharacterSheet();
    }

    loadCharacterSheet = () => {
        const character = this.props.character.character_id;
        axios.get('/api/characters/' + character + '/online')
            .then(res => {
                const online = res.data;
                console.log(online);
            });
        axios.get('/api/characters/' + character + '/attributes')
            .then(res => {
                const attributes = res.data;
                console.log(attributes);
            });
        axios.get('/api/characters/' + character + '/fatigue')
            .then(res => {
                const fatigue = res.data;
                console.log(fatigue);
            })
    }

    render() {
        return (
           <Card>

           </Card>
        )
    }
}

CharacterSheetTable.propTypes = {
    character: PropTypes.object
};

function mapStateToProps(state){
    return{
        user: state.user
    }
}

export default connect(mapStateToProps)(CharacterSheetTable);