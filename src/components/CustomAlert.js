import React from 'react';
import { Box, Alert, AlertTitle } from '@mui/material';

const CustomAlert = ({ alert, onClose }) => {
  if (!alert.visible) return null;

  return (
    <Box my={2}>
      <Alert severity={alert.type} onClose={onClose}>
        <AlertTitle>{alert.type === 'success' ? 'Success' : 'Error'}</AlertTitle>
        {alert.msg}
      </Alert>
    </Box>
  );
};

export default CustomAlert;
