import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

import { Table } from 'reactstrap';
import ReactPaginate from 'react-paginate';

import { Link } from 'react-router-dom';

class MailTable extends Component{


    constructor(props) {
        super(props);

        this.state = {
            mail: [],
            page: 1,
            totalPages: 1
        };
    }

    componentDidMount() {
        this.loadMail();
    }


    loadMail() {
        const characterId = this.props.id;
        axios.get('/api/characters/' + characterId + '/mail',{
            params: {
                page: this.state.page
            }
        })
            .then( res => {
                const mail = res.data.data;
                this.setState({
                    mail: mail,
                    totalPages: res.data.last_page
                });
            });
    }

    handlePageClick = (data) => {
        let selected = data.selected;
        this.setState({ page: selected }, () => {
            this.loadMail();
        });
    };

    render() {
        return (
            <Table responsive>
                <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                { this.state.mail.map(function (mail) {
                    return (
                        <tr key={mail.mail_id}>
                            <td><Link to={"/characters/" +  mail.from}>{ mail.sender ? mail.sender.name : 'Unknown' }</Link></td>
                            <td>{ mail.subject }</td>
                            <td>{ mail.timestamp }</td>
                        </tr>
                    )
                })}
                </tbody>

                <ReactPaginate previousLabel={"previous"}
                               nextLabel={"next"}
                               breakLabel={<a href="">...</a>}
                               breakClassName={"break-me"}
                               pageCount={this.state.totalPages}
                               marginPagesDisplayed={2}
                               pageRangeDisplayed={5}
                               onPageChange={this.handlePageClick}
                               containerClassName={"pagination"}
                               subContainerClassName={"pages pagination"}
                               activeClassName={"active"} />
            </Table>
        )
    }
}

MailTable.propTypes = {
    id: PropTypes.number
};

function mapStateToProps(state){
    return{
        user: state.user
    }
}

export default connect(mapStateToProps)(MailTable);