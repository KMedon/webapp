// MusicForm.js (shortened for clarity)
import React, { useEffect, useState } from 'react';
import { useSearchParams, useNavigate } from 'react-router-dom';
import { TextField, Button, Box } from '@mui/material';

const MusicForm = () => {
  const [searchParams] = useSearchParams();
  const id = searchParams.get('id');
  const navigate = useNavigate();

  const [formData, setFormData] = useState({
    title: '',
    artist: '',
    fileData: ''  // base64 of the MP3
  });

  useEffect(() => {
    if (id) {
      // We fetch existing record
      fetch(`/backend/getMusic.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
          if (data.result === 'SUCCESS') {
            // data.music => { id, title, artist, file_path, ... }
            setFormData({
              title: data.music.title,
              artist: data.music.artist,
              fileData: '', // we only set this if user wants to replace the MP3
            });
          }
        });
    }
  }, [id]);

  const handleChange = (e) => {
    setFormData(prev => ({ ...prev, [e.target.name]: e.target.value }));
  };

  // Reading the file as base64
  const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onloadend = () => {
      setFormData(prev => ({ ...prev, fileData: reader.result }));
    };
    reader.readAsDataURL(file); // convert file to base64
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    // If id => update, else => insert
    const endpoint = id ? '/backend/updateMusic.php' : '/backend/saveMusic.php';

    const response = await fetch(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ...formData, id: id ? id : undefined }),
    });
    const result = await response.json();

    if (result.result === 'SUCCESS') {
      alert('Success!');
      navigate('/music'); // or /music-list
    } else {
      alert(result.message || 'Error');
    }
  };

  return (
    <Box sx={{ maxWidth: 500, margin: '1rem auto' }}>
      <h2>{id ? 'Edit Music' : 'Add Music'}</h2>
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
          type="file"
          inputProps={{ accept: 'audio/*' }}
          onChange={handleFileChange}
          fullWidth
          margin="normal"
          helperText={formData.fileData ? 'File ready to upload' : 'Please select an audio file (.mp3)'}
        />
        <Button type="submit" variant="contained" color="primary" fullWidth>
          {id ? 'Update' : 'Save'}
        </Button>
      </form>
    </Box>
  );
}
export default MusicForm;
