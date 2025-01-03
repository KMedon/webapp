// src/components/MusicForm.js
import React, { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom'; 
import { TextField, Button, Box } from '@mui/material';

const VideoForm = () => {
  const [searchParams] = useSearchParams();
  const id = searchParams.get('id'); 

  const [formData, setFormData] = useState({
    id: '',
    title: '',
    artist: '',
    url: ''
  });

  useEffect(() => {
    if (id) {
      // Fetch existing video for edit
      fetch(`/backend/getVideos.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
          if (data.result === 'SUCCESS') {
            setFormData(data.video); 
          } else {
            alert(data.message || 'Error loading video');
          }
        })
        .catch(err => console.error(err));
    }
  }, [id]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (id) {
      // Update
      const response = await fetch('/backend/updateVideos.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ...formData, id }),
      });
      const result = await response.json();
      if (result.result === 'SUCCESS') {
        alert('Video updated successfully');
        window.location.href = '/video'; // or /video-list
      } else {
        alert(result.message || 'Error updating video');
      }
    } else {
      // Insert
      const response = await fetch('/backend/saveVideos.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });
      const result = await response.json();
      if (result.result === 'SUCCESS') {
        alert('Video created successfully');
        window.location.href = '/video'; // or /video-list
      } else {
        alert(result.message || 'Error saving video');
      }
    }
  };

  return (
    <Box sx={{ maxWidth: 500, margin: '1rem auto' }}>
      <h2>{id ? 'Edit Video' : 'Add Video'}</h2>
      <form onSubmit={handleSubmit}>
        <TextField
          label="Title"
          name="title"
          value={formData.title}
          onChange={handleChange}
          fullWidth
          margin="normal"
          required
        />
        <TextField
          label="Artist"
          name="artist"
          value={formData.artist}
          onChange={handleChange}
          fullWidth
          margin="normal"
          required
        />
        <TextField
          label="URL"
          name="url"
          value={formData.url}
          onChange={handleChange}
          fullWidth
          margin="normal"
          required
          helperText="e.g. https://example.com/audio.mp3"
        />
        <Button type="submit" variant="contained" color="primary">
          {id ? 'Update' : 'Save'}
        </Button>
      </form>
    </Box>
  );
};

export default VideoForm;
