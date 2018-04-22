import React, { Component } from 'react';
import PropTypes from 'prop-types';

import {
    Label,
    FormGroup,
    Input,
} from 'reactstrap';

class SearchField extends Component {
    constructor(props) {
        super(props);

        this.previousLength = 0;
    }

    inputChange = (data) => {
        let length = data.target.value.length;
        if (length >= 3){
            this.props.searchCallback(data.target.value);
        } else  if (this.previousLength === 3){
            this.props.searchCallback("");
        }
        this.previousLength = length;
    }

    render() {
        return (
            <FormGroup>
                <Label htmlFor="search">Search</Label>
                <Input type="text" id="search" onChange={this.inputChange}/>
            </FormGroup>
        )
    }
}

SearchField.propTypes = {
    searchCallback: PropTypes.func
};

export default SearchField;