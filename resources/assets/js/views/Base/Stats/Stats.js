import React, { Component } from 'react';

import {
    Row,
    Col,
    Card,
    CardBody,
} from 'reactstrap';

const brandPrimary = '#20a8d8';
const brandSuccess = '#4dbd74';
const brandInfo = '#63c2de';
const brandWarning = '#f8cb00';
const brandDanger = '#f86c6b';

class Stats extends Component {
    constructor(props) {
        super(props);

        this.state = {
            stats: []
        };
    }

    componentDidMount() {
        axios.get(`http://interbus.test/api/stats`)
            .then(res => {
                const stats = res.data;
                console.log(stats);
                this.setState({ stats });
            });
    }

    render() {
        return (
            <div className="animated fadeIn">
                <Row>
                {this.state.stats.map(function(stat) {
                    return (
                    <Col xs="12" sm="6" lg="3">
                        <Card className="text-white bg-primary">
                            <CardBody className="pb-0">
                                <h4 className="mb-0">{stat.value}</h4>
                                <p>{stat.stat}</p>
                            </CardBody>
                        </Card>
                    </Col>
                    );
                })}
                </Row>
            </div>
        )
    }
}

export default Stats;