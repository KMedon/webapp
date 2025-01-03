import React, { useState } from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import './style.css';
import TopBar from './components/TopBar'; 
import SideMenu from './components/SideMenu';
import Content from './components/Content';
import Music from './components/Music';
import MusicList from './components/MusicList';
import MusicForm from './components/MusicForm';
import GamesList from './components/GamesList';
import GameForm from './components/GameForm';
import UserForm from './components/UserForm';
import UserTable from './components/UserTable';
import VideoList from './components/VideoList';
import VideoForm from './components/VideoForm';
import Login from './components/Login';
import Logout from './components/Logout';


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
            <Route path="/music" element={<MusicList />} />
            <Route path="/music-form" element={<MusicForm />} />
            <Route path="/music-form/:id" element={<MusicForm />} />
            <Route path="/videos" element={<VideoList />} />
            <Route path="/video-form" element={<VideoForm />} />
            <Route path="/video-form/:id" element={<VideoForm />} />
            <Route path="/games" element={<GamesList />} />
            <Route path="/game-form" element={<GameForm />} />
            <Route path="/game-form/:id" element={<GameForm />} />
            <Route path="/user-form" element={<UserForm />} />
            <Route path="/user/:id/:formMode" element={<UserForm formMode='INSERT' />} />
            <Route path="/user/:id/:inputFormMode" element={<UserForm />} />
            <Route path="/usertable" element={<UserTable />} />
            <Route path="/user/:id" element={<UserForm />} /> {/* Dynamic route */}
            <Route path="/login" element={<Login />} />
            <Route path="/logout" element={<Logout />} />
            <Route path='/' element={<Music />} />
          </Routes>
        </Content>
      </div>
    </div>
  </Router>
  );
}

export default App;
