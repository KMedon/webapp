import React from "react";

function Hamburger({ onClick }) {
    return (
      <div className="hamburger" onClick={onClick}>
        <div></div>
        <div></div>
        <div></div>
      </div>
    );
  }
  
    export default Hamburger;