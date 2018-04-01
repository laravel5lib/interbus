import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

import { Table } from 'reactstrap';

class SkillsTable extends Component{


    constructor(props) {
        super(props);

        this.state = {
            skills: []
        };
    }

    componentDidMount() {
        this.loadContacts();
    }


    loadContacts() {
        const characterId = this.props.id;
        axios.get('/api/characters/' + characterId + '/skills')
            .then( res => {
                const skills = res.data.sort(function (a, b) {
                    if (a.skill_type.name < b.skill_type.name)
                        return -1;
                    if (a.skill_type.name > b.skill_type.name)
                        return 1;
                    return 0;
                });
                this.setState({
                    skills: skills
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
                </tr>
                </thead>
                <tbody>
                { this.state.skills.map(function (skill) {
                    return (
                        <tr>
                            <td>
                                <a href={'/types/' + skill.skill_id}>{skill.skill_type.name}</a>
                            </td>
                            <td>
                                {skill.trained_skill_level}
                            </td>
                        </tr>
                    )
                })}
                </tbody>
            </Table>
        )
    }
}

SkillsTable.propTypes = {
    id: PropTypes.number
};

function mapStateToProps(state){
    return{
        user: state.user
    }
}

export default connect(mapStateToProps)(SkillsTable);