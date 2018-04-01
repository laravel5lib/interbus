import { combineReducers } from 'redux';

import { user } from './userReducer';
import {reducer as toastrReducer} from 'react-redux-toastr'

const rootReducer = combineReducers({
    user,
    toastr: toastrReducer
});

export default rootReducer;