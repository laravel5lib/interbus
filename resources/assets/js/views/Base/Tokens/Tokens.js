import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import ReactPaginate from 'react-paginate';
import { BeatLoader } from 'react-spinners';
import {toastr} from 'react-redux-toastr'

import { history } from '../../../helpers/history';

import {
    Button,
    Label,
    Row,
    Col,
    FormGroup,
    Input,
    Card,
    CardHeader,
    CardFooter,
    CardBody,
    Table,
    Badge
} from 'reactstrap';
import { connect } from 'react-redux';

class Tokens extends Component {
    constructor(props) {
        super(props);

        this.state = {
            tokens: [],
            offset: 0,
            itemsPerPage: 20,
            ssoUrl: "#",
            loading: true,
            code: this.props.match.params.code
        };
    }

    loadFromServer(){
        var query = require('querystring');
        query = query.stringify( {offset: this.state['offset'], itemsPerPage: this.state['itemsPerPage'], q: this.query})
        axios.get('/api/tokens?' + query, {
            headers: {
                'Authorization' : 'Bearer ' + this.props.user.token
            }
        })
            .then(res => {
                const tokens = res.data;
                this.setState({ tokens: tokens.data, pageCount: Math.ceil(tokens.count / this.state.itemsPerPage) });
            });
    }

    loadSSO = () =>{
        this.setState({loading: true});
        axios.get('/api/ssourl')
            .then(res => {
                this.setState({ssoUrl: res.data})
                this.setState({loading: false});
            });
    }

    componentDidMount() {
        this.loadFromServer();
        this.loadSSO();

        let code = this.state['code'];
        if (code){
            history.push('/tokens');
            axios.post('/api/tokens/' + code)
                .then(res => {
                    this.loadFromServer();
                    toastr.success('Token Added', res.data.character_name + ' added.');
                });
        }
    }

    handlePageClick = (data) => {
        let selected = data.selected;
        let offset = Math.ceil(selected * this.state.itemsPerPage);
        this.setState({offset: offset}, () => {
            this.loadFromServer();
        });
    };

    render() {
        return (
            <div className="animated fadeIn">
                <Row>
                    <Col xs="12" lg="12">
                        <Card>
                            <CardHeader>
                                <i className="fa fa-align-justify"></i> Simple Table
                                <a href={this.state['ssoUrl']}><Button className="float-right" color="primary">Add Token</Button></a>
                            </CardHeader>
                            <CardBody>
                                <Table responsive>
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {this.state.tokens.map(function(token) {
                                        return (
                                            <tr key={token.id}>
                                                <td>{token.character_name}</td>
                                            </tr>
                                        );
                                    })}
                                    </tbody>
                                </Table>
                                <ReactPaginate previousLabel={"previous"}
                                               nextLabel={"next"}
                                               breakLabel={<a href="">...</a>}
                                               breakClassName={"break-me"}
                                               pageCount={this.state.pageCount}
                                               marginPagesDisplayed={2}
                                               pageRangeDisplayed={5}
                                               onPageChange={this.handlePageClick}
                                               containerClassName={"pagination"}
                                               subContainerClassName={"pages pagination"}
                                               activeClassName={"active"} />
                            </CardBody>
                        </Card>
                    </Col>
                </Row>
            </div>
        )
    }
}

function mapStateToProps(state) {
    return {
        user: state.user
    }
}

export default connect(mapStateToProps)(Tokens);