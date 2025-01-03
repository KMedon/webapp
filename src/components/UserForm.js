import React, { useState, useEffect } from 'react';
import { TextField, Button, Grid, Box, Typography, MenuItem } from '@mui/material';
import { DatePicker } from '@mui/x-date-pickers/DatePicker';
import { LocalizationProvider } from '@mui/x-date-pickers';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';
import CustomAlert from './CustomAlert';
import { useParams, useNavigate } from 'react-router-dom';

function UserForm() {
  const { id, formMode: inputFormMode } = useParams();
  const navigate = useNavigate();
  const [formMode, setFormMode] = useState(inputFormMode || 'INSERT'); // Default mode: INSERT

  const emptyFormData = {
    name: '',
    email: '',
    password: '',
    user_role: '',
    address: '',
    city: '',
    born_date: null,
    photo: '',
    video: '',
  };

 
  const [formData, setFormData] = useState(emptyFormData);
  const [alert, setAlert] = useState({
    visible: false,
    type: '',
    msg: '',
  });

  useEffect(() => {
    setFormMode(inputFormMode || 'INSERT'); // Update the formMode
    if (!id || inputFormMode === 'INSERT') {
      setFormData(emptyFormData); // Clear the form data for new user
    }
  }, [inputFormMode, id]);

  // Fetch user data if id is provided (for SEE or UPDATE modes)
  useEffect(() => {
    if (id && formMode !== 'INSERT') {
      const fetchUserData = async () => {
        try {
          const response = await fetch(`/backend/findUser.php?id=${id}`);
          const result = await response.json();

          if (result.result === 'SUCCESS' && result.data.length > 0) {
            const user = result.data[0];
            user.photo = user.photo || '';
            if (user.born_date) {
              user.born_date = new Date(user.born_date); // Parse the date
            }
            
            setFormData(user);
            
          } else {
            setAlert({
              visible: true,
              type: 'error',
              msg: 'Could not fetch user',
            });
            setFormData(emptyFormData);
          }
        } catch (error) {
          setAlert({
            visible: true,
            type: 'error',
            msg: 'Error fetching user data',
          });
        }
      };
      fetchUserData();
    }
  }, [id, formMode]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({
      ...formData,
      [name]: value,
    });
  };

  const handleDateChange = (date) => {
    setFormData({
      ...formData,
      born_date: date,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await fetch('/backend/saveUser.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ...formData, mode: formMode }),
      });

      const result = await response.json();
      if (result.result === 'SUCCESS') {
        setAlert({
          visible: true,
          type: 'success',
          msg: 'User saved successfully!',
        });
        if (formMode === 'INSERT') {
          setFormData(emptyFormData);
        } else {
          navigate('/usertable');
        }
      } else {
        setAlert({
          visible: true,
          type: 'error',
          msg: result.message || 'Error saving user!',
        });
      }
    } catch (error) {
      setAlert({
        visible: true,
        type: 'error',
        msg: 'Failed to send data. Try again later.',
      });
    }
  };

  const onClose = () => {
    navigate('/usertable'); // Navigate back to the User Table view
  };

  const onUpdateUser = async () => {
    try {
      const response = await fetch(`/backend/updateUser.php?id=${id}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });
      const result = await response.json();
      if (response.ok && result.result === 'SUCCESS') {
      setAlert({ visible: true, type: 'success', msg: result.message });
      setFormMode('SEE'); // Change the form mode to SEE after a successful update
    } else {
      throw new Error(result.message || 'Update failed');
      }
    } catch (error) {
      setAlert({
        visible: true,
        type: 'error',
        msg: `Update failed: ${error.message}`,
      });
    }
  };
  
  const onCancelUpdate = () => {
    setFormMode('SEE'); // Simply revert to SEE mode without saving
  
  };
  
  const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setFormData({ ...formData, photo: reader.result});
    };
    reader.readAsDataURL(file);
  }
};

const handleVideoChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onloadend = () => {
      setFormData({ ...formData, video: reader.result });
    };
    reader.readAsDataURL(file);  // This converts the file to base64
  }
};


  return (
    <Box sx={{ mt: 4, width: '50%', margin: '0 auto' }}>
      <Typography variant="h4" gutterBottom>
        {formMode === 'SEE' ? 'View User' : formMode === 'UPDATE' ? 'Edit User' : 'Add User'}
      </Typography>
      <CustomAlert
        alert={alert}
        onClose={() => setAlert({ visible: false, msg: '', type: '' })}
      />
      <form onSubmit={handleSubmit}>
        <Grid container spacing={2}>
          {/* Name */}
          <Grid item xs={12}>
            <TextField
              label="Name"
              name="name"
              value={formData.name}
              onChange={handleChange}
              fullWidth
              required
              InputProps={{
                readOnly: formMode === 'SEE',
              }}
            />
          </Grid>

          {/* Email */}
          <Grid item xs={6}>
            <TextField
              label="Email"
              name="email"
              type="email"
              value={formData.email}
              onChange={handleChange}
              fullWidth
              required
              InputProps={{
                readOnly: formMode === 'SEE',
              }}
            />
          </Grid>
          
          {/* Password */}
          <Grid item xs={6}>
            {formMode === 'INSERT' && (
              <TextField
                label="Password"
                name="password"
                type="password"
                value={formData.password}
                onChange={handleChange}
                fullWidth
                required
              />
            )}
          </Grid>

          {formData.photo && (
            <Grid item xs={12}>
              <img src={formData.photo} alt="User Photo" style={{ maxWidth: '150px', maxHeight: '150px', borderRadius: '8px' }} />
            </Grid>
          )}
          {(formMode === 'INSERT' || formMode === 'UPDATE') && (
            <Grid item xs={12}>
              <Typography>Photo</Typography>
              <input type='file' onChange={handleFileChange} />

              {formData.photo && (<Button variant='contained' color='primary' onClick={() => { setFormData({ ...formData, photo: ''}); }}>
                Remove
              </Button>)}
            </Grid>
          )}
          
          {(formMode === 'INSERT' || formMode === 'UPDATE') && (
          <Grid item xs={12}>
            <Typography>Presentation Video</Typography>
              <input 
                type="file" 
                accept="video/*" 
                onChange={handleVideoChange} 
              />
          {formData.video && (
            <Button
              variant="contained"
              color="primary"
              onClick={() => setFormData({ ...formData, video: '' })}
            >
              Remove Video
            </Button>
          )}
        </Grid>
        )}


          {/* User Role */}
          <Grid item xs={12}>
            <TextField
              label="User Role"
              name="user_role"
              value={formData.user_role}
              onChange={handleChange}
              fullWidth
              select={formMode !== 'SEE'}
              InputProps={{
                readOnly: formMode === 'SEE',
              }}
            >
              <MenuItem value="Admin">Admin</MenuItem>
              <MenuItem value="User">User</MenuItem>
              <MenuItem value="Editor">Editor</MenuItem>
              <MenuItem value="Provider">Provider</MenuItem>
            </TextField>
          </Grid>

          {/* Address */}
          <Grid item xs={12}>
            <TextField
              label="Address"
              name="address"
              value={formData.address}
              onChange={handleChange}
              fullWidth
              InputProps={{
                readOnly: formMode === 'SEE',
              }}
            />
          </Grid>

          {/* City */}
          <Grid item xs={12}>
            <TextField
              label="City"
              name="city"
              value={formData.city}
              onChange={handleChange}
              fullWidth
              InputProps={{
                readOnly: formMode === 'SEE',
              }}
            />
          </Grid>

          {/* Date of Birth */}
          <Grid item xs={12}>
            <LocalizationProvider dateAdapter={AdapterDateFns}>
              <DatePicker
                label="Date of Birth"
                value={formData.born_date}
                onChange={handleDateChange}
                renderInput={(params) => (
                  <TextField {...params} fullWidth InputProps={{ readOnly: formMode === 'SEE' }} />
                )}
              />
            </LocalizationProvider>
          </Grid>

          {/* Buttons */}
          <Grid item xs={12}>
            <Box sx={{ display: 'flex', justifyContent: 'flex-end', gap: 2 }}>
            {formMode === 'INSERT' && (
              <Button type="submit" variant="contained" color="primary">
              Save User
              </Button>
            )}
          {formMode === 'SEE' && (
            <Button
            variant="contained"
            color="primary"
            onClick={onClose}
            >
            Close
          </Button>
          )}
         {formMode === 'UPDATE' && (
        <>
        <Button
          variant="contained"
          color="primary"
          onClick={onUpdateUser}
        >
          Save
        </Button>
        <Button
          variant="contained"
          color="secondary"
          onClick={onCancelUpdate}
        >
          Cancel
        </Button>
      </>
    )}
  </Box>
</Grid>

        </Grid>
      </form>
    </Box>
  );
}

export default UserForm;
