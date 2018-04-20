import React, { Component } from 'react';
import ReactPaginate from 'react-paginate';

import {
    Label,
    Row,
    Col,
    FormGroup,
    Input,
    Card,
    CardHeader,
    CardBody,
    Table,
} from 'reactstrap';

class Characters extends Component {
    constructor(props) {
        super(props);

        this.query = "";
        this.previousLength = 0;
        this.state = {
            characters: [],
            offset: 0,
            itemsPerPage: 20,
        };
    }

    loadFromServer(){
        axios.get(`/api/characters?offset=` + this.state['offset'] + "&itemsPerPage=" + this.state['itemsPerPage'] + "&q=" + this.query)
            .then(res => {
                const characters = res.data;
                this.setState({
                    characters: characters.data,
                    pageCount: Math.ceil(characters.count / this.state.itemsPerPage)
                });
            });
    }

    componentDidMount() {
        this.loadFromServer();
    }

    handlePageClick = (data) => {
        let selected = data.selected;
        let offset = Math.ceil(selected * this.state.itemsPerPage);
        this.setState({offset: offset}, () => {
            this.loadFromServer();
        });
    };

    inputChange = (data) => {
        let length = data.target.value.length;
        if (length >= 3){
            this.query = data.target.value;
            this.loadFromServer();
        }else if (this.previousLength === 3){
            this.query = "";
            this.loadFromServer();
        }
        this.previousLength = length;
    }

    render() {
        return (
            <div className="animated fadeIn">

                <FormGroup>
                    <Label htmlFor="search">Search</Label>
                    <Input type="text" id="search" onChange={this.inputChange}/>
                </FormGroup>


                <Row>
                    <Col xs="12" lg="12">
                        <Card>
                            <CardHeader>
                                <i className="fa fa-align-justify"></i> Simple Table
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
                                                <td><a href={'/characters/' + char.character_id}>{char.name}</a></td>
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