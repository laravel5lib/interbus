import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

import { Table, Card, CardHeader } from 'reactstrap';

import { Link } from 'react-router-dom';

class SkillsTable extends Component{


    constructor(props) {
        super(props);

        this.state = {
            skills: [],
            keys: [],
        };
    }

    componentDidMount() {
        this.loadContacts();
    }


    loadContacts() {
        const characterId = this.props.id;
        axios.get('/api/characters/' + characterId + '/skills')
            .then( res => {
                const skills = _.groupBy(res.data, 'skill_type.group.name');

                let sortedSkills = {};

                Object.keys(skills).map(function (key) {
                    const sorted = skills[key].sort(function (a, b) {
                        if (a.skill_type.name < b.skill_type.name)
                            return -1;
                        if (a.skill_type.name > b.skill_type.name)
                            return 1;
                        return 0;
                    });
                    sortedSkills[key] = sorted;
                });

                this.setState({
                    skills: sortedSkills,
                    keys: Object.keys(skills).sort(),
                });
            });
    }

    render() {
        return (
            <div>
                { this.state.keys.map(function (key) {
                    return (
                        <Card key={key}>
                            <CardHeader>
                                <i className="fa fa-align-justify"></i> {key}
                            </CardHeader>
                        <Table responsive>
                            <tbody>
                            {this.state.skills[key].map(function (skill) {
                                return (
                                    <tr key={skill.skill_id}>
                                        <td>
                                            <Link to={'/types/' + skill.skill_id}>{skill.skill_type.name}</Link>
                                        </td>
                                        <td>
                                            {skill.trained_skill_level}
                                        </td>
                                    </tr>
                                )
                            })}
                            </tbody>
                        </Table>
                        </Card>
                    )
                }, this)}
            </div>
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