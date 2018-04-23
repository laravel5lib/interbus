import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

import { Table, Card, CardHeader } from 'reactstrap';

import { Link } from 'react-router-dom';

class SkillQueueTable extends Component{


    constructor(props) {
        super(props);

        this.state = {
            skills: [],
        };
    }

    componentDidMount() {
        this.loadSkillQueue();
    }


    loadSkillQueue = () => {
        const characterId = this.props.id;
        axios.get('/api/characters/' + characterId + '/queue')
            .then( res => {
                const skills = _.orderBy(res.data, 'queue_position', 'asc');
                this.setState({
                    skills: skills,
                });
            });
    }

    render() {
        return (
            <Table responsive>
                <thead>
                <tr>
                    <th>Skill</th>
                    <th>Level</th>
                    <th>Finish Date</th>
                </tr>
                </thead>
                <tbody>
                { this.state.skills.map(function (skill) {
                    return (
                        <tr>
                            <td>{ skill.type.name }</td>
                            <td>{ skill.finished_level - 1 } -> { skill.finished_level }</td>
                            <td>{ skill.finish_date }</td>
                        </tr>
                    )
                })}
                </tbody>
            </Table>
        )
    }
}

SkillQueueTable.propTypes = {
    id: PropTypes.number
};

function mapStateToProps(state){
    return{
        user: state.user
    }
}

export default connect(mapStateToProps)(SkillQueueTable);