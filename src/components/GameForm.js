// src/components/GameForm.js
import React, { useEffect, useState } from 'react';
import { useSearchParams, useNavigate } from 'react-router-dom';

const GameForm = () => {
  const [searchParams] = useSearchParams();
  const id = searchParams.get('id');
  const navigate = useNavigate();

  const [formData, setFormData] = useState({
    title: '',
    description: '',
    iframe_url: ''
  });

  useEffect(() => {
    if (id) {
      // Fetch existing game
      fetch(`/backend/getGame.php?id=${id}`)
        .then(res => res.json())
        .then(result => {
          if (result.result === 'SUCCESS') {
            // { id, title, description, iframe_url }
            setFormData({
              title: result.game.title,
              description: result.game.description,
              iframe_url: result.game.iframe_url
            });
          } else {
            alert(result.message || 'Error loading game');
          }
        })
        .catch(err => console.error('getGame error:', err));
    }
  }, [id]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    let endpoint = '/backend/saveGame.php';
    if (id) endpoint = '/backend/updateGame.php';

    const response = await fetch(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ...formData, id }),
    });
    const result = await response.json();
    if (result.result === 'SUCCESS') {
      alert(id ? 'Game updated!' : 'Game saved!');
      navigate('/games');
    } else {
      alert(result.message || 'Error saving game');
    }
  };

  return (
    <div style={{ maxWidth: '600px', margin: 'auto' }}>
      <h2>{id ? 'Edit Game' : 'Add Game'}</h2>
      <form onSubmit={handleSubmit}>
        <label>Title:</label>
        <input
          name="title"
          value={formData.title}
          onChange={handleChange}
          required
          style={{ display: 'block', marginBottom: 10, width: '100%' }}
        />

        <label>Description:</label>
        <textarea
          name="description"
          value={formData.description}
          onChange={handleChange}
          style={{ display: 'block', marginBottom: 10, width: '100%', height: '80px' }}
        />

        <label>Game Iframe URL:</label>
        <input
          name="iframe_url"
          value={formData.iframe_url}
          onChange={handleChange}
          required
          style={{ display: 'block', marginBottom: 10, width: '100%' }}
          placeholder="https://example.com/my-game-embed"
        />

        <button type="submit">{id ? 'Update' : 'Save'}</button>
      </form>
    </div>
  );
};

export default GameForm;
