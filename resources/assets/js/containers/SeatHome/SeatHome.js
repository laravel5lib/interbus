import React, {Component} from 'react';
import {Switch, Route, Redirect} from 'react-router-dom';
import {Container} from 'reactstrap';
import Header from '../../components/Header/Header';
import Sidebar from '../../components/Sidebar/Sidebar';
import Breadcrumb from '../../components/Breadcrumb/Breadcrumb';
import Aside from '../../components/Aside/Aside';
import Footer from '../../components/Footer/Footer';

import Dashboard from '../../views/Dashboard/Dashboard';
import Stats from '../../views/Base/Stats/Stats';
import Characters from '../../views/Base/Characters/Characters';
import Character from '../../views/Base/Characters/Character';
import Tokens from '../../views/Base/Tokens/Tokens';
import ReduxToastr from 'react-redux-toastr'
import { connect } from 'react-redux';
class SeatHome extends Component {

    componentDidMount() {

    }

    render() {
        return (
            <div className="app">
                <ReduxToastr
                    timeOut={4000}
                    newestOnTop={false}
                    position="bottom-right"
                    transitionIn="fadeIn"
                    transitionOut="fadeOut"
                    />
                <Header/>
                <div className="app-body">
                    <Sidebar {...this.props}/>
                    <main className="main">
                        <Breadcrumb/>
                        <Container fluid>
                            <Switch>
                                <Route path="/dashboard" name="Dashboard" component={Dashboard}/>
                                <Route path="/stats" name="Stats" component={Stats}/>
                                <Route path="/characters/:id" name="Character" component={Character}/>
                                <Route path="/characters" name="Characters" component={Characters}/>
                                <Route path="/tokens/:code" name="Tokens" component={Tokens}/>
                                <Route path="/tokens" name="Tokens" component={Tokens}/>
                                <Route path={"/chat"} name={"Chat"} component={Characters}/>
                                <Route path={"/chat/:id"} name={"Chat Channel"} component={Character}/>
                                <Route path={"/types"} name={"Types"} component={Characters} />
                                <Route path={"/types/:id"} name={"Type"} component={Character} />
                                <Redirect from="/" to="/dashboard"/>
                            </Switch>
                        </Container>
                    </main>
                    <Aside/>
                </div>
                <Footer/>
            </div>
        );
    }
}

function mapStateToProps(state) {
    return {
        user: state.user
    }
}


const home = connect(mapStateToProps)(SeatHome);
export { home as SeatHome };

