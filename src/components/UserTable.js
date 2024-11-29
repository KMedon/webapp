import React, { useEffect, useState, useCallback } from 'react';
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import { Button } from 'primereact/button';
import { useNavigate } from 'react-router-dom';
import { Box, Typography } from '@mui/material';
import ConfirmationDialog from './ConfirmationDialog';
import { Button as MUIButton } from '@mui/material';
import 'primereact/resources/themes/saga-blue/theme.css';
import 'primereact/resources/primereact.min.css';
import 'primeicons/primeicons.css';

const UserTable = () => {
  const [users, setUsers] = useState([]);
  const [setAlert] = useState({ visible: false, type: '', msg: '' });
  const [loading, setLoading] = useState(false);
  const [first, setFirst] = useState(0);
  const [rows, setRows] = useState(10);
  const [totalRecords, setTotalRecords] = useState(0);
  const [sortField, setSortField] = useState(null);
  const [sortOrder, setSortOrder] = useState(null);
  const [dialogOpen, setDialogOpen] = useState(false); // Manage dialog visibility
  const [selectedUserId, setSelectedUserId] = useState(null);
  const navigate = useNavigate();

  const fetchUsers = useCallback(async () => {
    setLoading(true);
    try {
      const response = await fetch(`/backend/listUsers.php?first=${first}&rows=${rows}&sortField=${sortField}&sortOrder=${sortOrder}`);
      const result = await response.json();
      if (result.result === "SUCCESS") {
        setUsers(result.data);
        setTotalRecords(result.totalRecords || 0);
      } else {
        console.error(result.message);
        setUsers([]);
        setTotalRecords(0);
      }
    } catch (error) {
      console.error("Error fetching users:", error);
      setUsers([]);
      setTotalRecords(0);
    } finally {
      setLoading(false);
    }
  }, [first, rows, sortField, sortOrder]);
  
  useEffect(() => {
    fetchUsers();
  }, [fetchUsers]);

  const handleDeleteClick = (rowData) => {
    setSelectedUserId(rowData.id);
    setDialogOpen(true);
  };

  const handleDeleteConfirmation = async () => {
    try {
      const response = await fetch(`/backend/deleteUser.php?id=${selectedUserId}`, {
        method: 'POST',
      });
      const result = await response.json();
      if (result.result === 'SUCCESS') {
        setAlert({ visible: true, type: 'success', msg: 'User deleted successfully!' });
        setUsers((prevUsers) => prevUsers.filter((user) => user.id !== selectedUserId));
      } else {
        setAlert({ visible: true, type: 'error', msg: result.message || 'Error deleting user!' });
      }
    } catch (error) {
      setAlert({ visible: true, type: 'error', msg: 'Failed to delete user. Try again later.' });
    }
    setDialogOpen(false); // Close the dialog
  };


  const actionTemplate = (rowData) => (
    <div className="p-buttonset">
      <Button
        icon="pi pi-eye"
        className="p-button-rounded p-button-info"
        onClick={() => navigate(`/user/${rowData.id}/SEE`)}
      />
      <Button
        icon="pi pi-pencil"
        className="p-button-rounded p-button-warning"
        onClick={() => navigate(`/user/${rowData.id}/UPDATE`)}
      />
      <Button
        icon="pi pi-trash"
        className="p-button-rounded p-button-danger"
        onClick={() => handleDeleteClick(rowData)}
      />
    </div>
  );

  return (
    <Box sx={{ mt: 4, width: '70%', margin: '0 auto' }}>
      <Typography variant="h4" gutterBottom>
        User Table
      </Typography>
      <Box sx={{ display: 'flex', justifyContent: 'flex-start', mb: 2 }}>
        <MUIButton
        variant="contained"
        startIcon={<i className="pi pi-plus"></i>}
        onClick={() => navigate(`/user-form`)}
        style={{ backgroundColor: 'green', color: 'white' }}
        >
        Add New User
        </MUIButton>
      </Box>
      <ConfirmationDialog
        open={dialogOpen}
        title="Delete user"
        description="Are you sure you want to delete the user?"
        onClose={() => setDialogOpen(false)}
        response={handleDeleteConfirmation}
      />
      <DataTable
        value={users}
        lazy
        paginator
        first={first}
        rows={rows}
        totalRecords={totalRecords}
        loading={loading}
        onPage={(e) => {
          setFirst(e.first);
          setRows(e.rows);
        }}
        onSort={(e) => {
          setSortField(e.sortField);
          setSortOrder(e.sortOrder === 1 ? 'ASC' : 'DESC');
        }}
        sortField={sortField}
        sortOrder={sortOrder}
        rowsPerPageOptions={[5, 10, 20, 50, 100]}
        responsiveLayout="scroll"
      >
        <Column field="name" header="Name" sortable />
        <Column field="email" header="Email" sortable />
        <Column field="user_role" header="Role" sortable />
        <Column body={actionTemplate} header="Actions" />
      </DataTable>
    </Box>
    
  );
};

export default UserTable;
