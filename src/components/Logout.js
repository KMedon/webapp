// src/components/Logout.js
import React, { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';

function Logout() {
  const navigate = useNavigate();

  useEffect(() => {
    // Immediately call /logout.php
    const doLogout = async () => {
      try {
        const response = await fetch('/backend/logout.php', {
          method: 'GET',
          credentials: 'include'
        });
        await response.json(); // we don't really care about the result beyond success
      } catch (err) {
        console.error('Logout error:', err);
      }
      // After calling logout, redirect to /login or home
      navigate('/login');
    };
    doLogout();
  }, [navigate]);

  return (
    <div style={{ margin: '1rem' }}>
      <h3>Logging out...</h3>
    </div>
  );
}

export default Logout;
