import React from 'react';

function Content({ isFullSize, children }) {
    return (
      <div className={`content ${isFullSize ? 'full-size' : ''}`}>
        {children}
      </div>
    );
  }
  
    export default Content;