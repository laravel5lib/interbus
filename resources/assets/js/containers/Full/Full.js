import React, {Component} from 'react';
import {Switch, Route, Redirect} from 'react-router-dom';
import {Container} from 'reactstrap';
import Header from '../../components/Header/Header';
import Sidebar from '../../components/Sidebar/Sidebar';
import Breadcrumb from '../../components/Breadcrumb/Breadcrumb';
import Aside from '../../components/Aside/Aside';
import Footer from '../../components/Footer/Footer';

import Dashboard from '../../views/Dashboard/Dashboard';

import Colors from '../../views/Theme/Colors/Colors';
import Typography from '../../views/Theme/Typography/Typography';

import Charts from '../../views/Charts/Charts';
import Widgets from '../../views/Widgets/Widgets';

// Base
import Cards from '../../views/Base/Cards/Cards';
import Forms from '../../views/Base/Forms/Forms';
import Switches from '../../views/Base/Switches/Switches';
import Tables from '../../views/Base/Tables/Tables';
import Tabs from '../../views/Base/Tabs/Tabs';
import Breadcrumbs from '../../views/Base/Breadcrumbs/Breadcrumbs';
import Carousels from '../../views/Base/Carousels/Carousels';
import Collapses from '../../views/Base/Collapses/Collapses';
import Dropdowns from '../../views/Base/Dropdowns/Dropdowns';
import Jumbotrons from '../../views/Base/Jumbotrons/Jumbotrons';
import ListGroups from '../../views/Base/ListGroups/ListGroups';
import Navbars from '../../views/Base/Navbars/Navbars';
import Navs from '../../views/Base/Navs/Navs';
import Paginations from '../../views/Base/Paginations/Pagnations';
import Popovers from '../../views/Base/Popovers/Popovers';
import ProgressBar from '../../views/Base/ProgressBar/ProgressBar';
import Tooltips from '../../views/Base/Tooltips/Tooltips';

// Buttons
import Buttons from '../../views/Buttons/Buttons/Buttons';
import ButtonDropdowns from '../../views/Buttons/ButtonDropdowns/ButtonDropdowns';
import ButtonGroups from '../../views/Buttons/ButtonGroups/ButtonGroups';
import SocialButtons from '../../views/Buttons/SocialButtons/SocialButtons';

// Icons
import Flags from '../../views/Icons/Flags/Flags';
import FontAwesome from '../../views/Icons/FontAwesome/FontAwesome';
import SimpleLineIcons from '../../views/Icons/SimpleLineIcons/SimpleLineIcons';

// Notifications
import Alerts from '../../views/Notifications/Alerts/Alerts';
import Badges from '../../views/Notifications/Badges/Badges';
import Modals from '../../views/Notifications/Modals/Modals';

class Full extends Component {
  render() {
    return (
      <div className="app">
        <Header/>
        <div className="app-body">
          <Sidebar {...this.props}/>
          <main className="main">
            <Breadcrumb/>
            <Container fluid>
              <Switch>
                <Route path="/dashboard" name="Dashboard" component={Dashboard}/>
                <Route path="/theme/colors" name="Colors" component={Colors}/>
                <Route path="/theme/typography" name="Typography" component={Typography}/>
                <Route path="/base/cards" name="Cards" component={Cards}/>
                <Route path="/base/forms" name="Forms" component={Forms}/>
                <Route path="/base/switches" name="Swithces" component={Switches}/>
                <Route path="/base/tables" name="Tables" component={Tables}/>
                <Route path="/base/tabs" name="Tabs" component={Tabs}/>
                <Route path="/base/breadcrumbs" name="Breadcrumbs" component={Breadcrumbs}/>
                <Route path="/base/carousels" name="Carousels" component={Carousels}/>
                <Route path="/base/collapses" name="Collapses" component={Collapses}/>
                <Route path="/base/dropdowns" name="Dropdowns" component={Dropdowns}/>
                <Route path="/base/jumbotrons" name="Jumbotrons" component={Jumbotrons}/>
                <Route path="/base/list-groups" name="ListGroups" component={ListGroups}/>
                <Route path="/base/navbars" name="Navbars" component={Navbars}/>
                <Route path="/base/navs" name="Navs" component={Navs}/>
                <Route path="/base/paginations" name="Paginations" component={Paginations}/>
                <Route path="/base/popovers" name="Popovers" component={Popovers}/>
                <Route path="/base/progress-bar" name="Progress Bar" component={ProgressBar}/>
                <Route path="/base/tooltips" name="Tooltips" component={Tooltips}/>
                <Route path="/buttons/buttons" name="Buttons" component={Buttons}/>
                <Route path="/buttons/button-dropdowns" name="ButtonDropdowns" component={ButtonDropdowns}/>
                <Route path="/buttons/button-groups" name="ButtonGroups" component={ButtonGroups}/>
                <Route path="/buttons/social-buttons" name="Social Buttons" component={SocialButtons}/>
                <Route path="/icons/flags" name="Flags" component={Flags}/>
                <Route path="/icons/font-awesome" name="Font Awesome" component={FontAwesome}/>
                <Route path="/icons/simple-line-icons" name="Simple Line Icons" component={SimpleLineIcons}/>
                <Route path="/notifications/alerts" name="Alerts" component={Alerts}/>
                <Route path="/notifications/badges" name="Badges" component={Badges}/>
                <Route path="/notifications/modals" name="Modals" component={Modals}/>
                <Route path="/widgets" name="Widgets" component={Widgets}/>
                <Route path="/charts" name="Charts" component={Charts}/>
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

export default Full;
