import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

import { Table, Card, CardHeader } from 'reactstrap';

import { Link } from 'react-router-dom';

class ClonesTable extends Component{

    constructor(props) {
        super(props);

        this.state = {
            clones: [],
        };
    }

    componentDidMount() {
        this.loadClones();
    }

    loadClones() {
        const characterId = this.props.id;
        axios.get('/api/characters/' + characterId + '/clones')
            .then( res => {
                const clones = res.data
                this.setState({
                    clones: clones
                });
            });
    }


    render() {
        return (
            <div>
            { this.state.clones.map(function (clone) {
                console.log(clone);
                return (
                    <Card key={clone.jump_clone_id}>
                        <CardHeader>
                            <i className="fa fa-align-justify"></i> {clone.location ? clone.location.name : 'Unknown'}
                        </CardHeader>
                        <Table responsive>
                            <tbody>
                            {clone.implants.map(function (implant) {
                                return (
                                    <tr key={implant.implant}>
                                        <td>
                                            {implant.type.name}
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

ClonesTable.propTypes = {
    id: PropTypes.number
};

function mapStateToProps(state){
    return{
        user: state.user
    }
}

export default connect(mapStateToProps)(ClonesTable);