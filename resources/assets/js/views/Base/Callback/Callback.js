import React, { Component } from 'react';
import { authHeader } from '../../../helpers/authHeader'

import { history } from '../../../helpers/history';

import {
    Button,
    Row,
    Col,
} from 'reactstrap';

class Callback extends Component {
    constructor(props) {
        super(props);

        this.state = {
            code: this.props.match.params.code
        };

    }

    componentDidMount(){
        let code = this.state['code'];
        if (code){
            axios.post('/api/tokens/' + code,
                {

                },
                {
                    headers: authHeader()
                })
                .then(res => {
                    history.push('/tokens');
                });
        }
    }

    render() {
        return (
            <div className="animated fadeIn">
                <Row>
                    <Col xs="12" lg="12">
                    </Col>
                </Row>
            </div>
        )
    }
}

export default Callback;