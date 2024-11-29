import React from "react";
import Hamburger from "./Hamburger";


function TopBar({ onToggleMenu }) {
    return (
      <div className="top">
        <Hamburger onClick={onToggleMenu} />
        <span>HEAD</span>
      </div>
    );
  }

  export default TopBar;