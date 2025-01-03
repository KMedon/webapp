import React from 'react';
import { NavLink } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faGamepad, faMusic, faVideo, faUser } from '@fortawesome/free-solid-svg-icons';

//import '../style.css';

function SideMenu() {
  return (
    <nav className="side-menu">
      <ul>
        <li>
          <NavLink 
            to="/music"
            className={({ isActive }) => (isActive ? "active" : "")}>
              <FontAwesomeIcon icon={faMusic} /> Music
          </NavLink>
        </li>
        <li>
          <NavLink 
            to="/videos"
            className={({ isActive }) => (isActive ? "active" : "")}>
               <FontAwesomeIcon icon={faVideo} /> Videos
          </NavLink>
        </li>
        <li>
          <NavLink 
            to="/games"
            className={({ isActive }) => (isActive ? "active" : "")}>
               <FontAwesomeIcon icon={faGamepad} /> Games
          </NavLink>
        </li>
        <li>
          <NavLink 
            to="/user-form"
            className={({ isActive }) => (isActive ? "active" : "")}>
               <FontAwesomeIcon icon={faUser} /> User Form
          </NavLink>  
        </li>
        <li>
          <NavLink to="/usertable" className={({ isActive }) => (isActive ? 'active' : '')}>
            <FontAwesomeIcon icon={faUser} /> Manage Users
          </NavLink>
        </li>
        {/* Add a Login link */}
        <li>
          <NavLink to="/login">
            <FontAwesomeIcon icon={faUser} /> Login
          </NavLink>
        </li>
        <li>
          <NavLink to="/logout">
           Logout
          </NavLink>
        </li>
      </ul>
    </nav>
  );
}

export default SideMenu;
