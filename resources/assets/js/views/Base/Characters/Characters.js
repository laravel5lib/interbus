import React, { Component } from 'react';
import ReactPaginate from 'react-paginate';
import SearchField from '../../../components/Search/SearchField';
import { Link } from 'react-router-dom';

import {
    Row,
    Col,
    Card,
    CardHeader,
    CardBody,
    Table,
} from 'reactstrap';

class Characters extends Component {
    constructor(props) {
        super(props);

        this.query = "";
        this.state = {
            characters: [],
            query: "",
            page: 1,
            pageCount: 0
        };
    }

    searchCallback = (query) => {
        //Reset page for new search results
        this.setState({
            query,
            page: 1,
        }, () => {
            this.loadFromServer();
        });
    }

    loadFromServer = () => {
        axios.get('/api/characters/', {
            params: {
                q: this.state.query,
                page: this.state.page,
            }
        })
        .then(res => {
            const characters = res.data;
            this.setState({
                characters: characters.data,
                pageCount: characters.last_page,
            });
        });
    }

    componentDidMount = () => {
        this.loadFromServer();
    }

    handlePageClick = (data) => {
        let selected = data.selected;
        this.setState({ page: selected }, () => {
            this.loadFromServer();
        });
    };

    render() {
        return (
            <div className="animated fadeIn">

                <SearchField searchCallback={this.searchCallback}/>

                <Row>
                    <Col xs="12" lg="12">
                        <Card>
                            <CardHeader>
                                <i className="fa fa-align-justify"></i>Characters
                            </CardHeader>
                            <CardBody>
                                <Table responsive>
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Corporation</th>
                                        <th>Alliance</th>
                                        <th>Birthday</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {this.state.characters.map(function(char) {
                                        return (
                                            <tr>
                                                <td><Link to={'/characters/' + char.character_id}>{char.name}</Link></td>
                                                <td>
                                                    {char.corporation && char.corporation.name}
                                                </td>
                                                <td>
                                                    {char.alliance && char.alliance.name}
                                                </td>
                                                <td>{char.birthday}</td>
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

export default Characters;