import React, { useState } from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import './style.css';
import TopBar from './components/TopBar'; 
import SideMenu from './components/SideMenu';
import Content from './components/Content';
import Music from './components/Music';
import Videos from './components/Videos';
import Games from './components/Games'; 
import UserForm from './components/UserForm';
import UserTable from './components/UserTable';

function App() {
  const [menuVisible, setMenuVisible] = useState(true);

  const toggleSideMenu = () => {
    setMenuVisible(!menuVisible);
  };

  return (
    <Router>
    <div className="root">
      <TopBar onToggleMenu={toggleSideMenu} />
      <div className="container">
        {menuVisible && <SideMenu />}
        <Content isFullSize={!menuVisible}>
          <Routes>
            <Route path="/music" element={<Music />} />
            <Route path="/videos" element={<Videos />} />
            <Route path="/games" element={<Games />} />
            <Route path="/user-form" element={<UserForm />} />
            <Route path="/user/:id/:formMode" element={<UserForm formMode='INSERT' />} />
            <Route path="/user/:id/:inputFormMode" element={<UserForm />} />
            <Route path="/usertable" element={<UserTable />} />
            <Route path="/user/:id" element={<UserForm />} /> {/* Dynamic route */}
            <Route path='/' element={<Music />} />
          </Routes>
        </Content>
      </div>
    </div>
  </Router>
  );
}

export default App;
