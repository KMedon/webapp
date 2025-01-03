import React, { useEffect, useState} from 'react';
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import { Button } from 'primereact/button';
import { Dialog, DialogContent } from '@mui/material';
import { useNavigate } from 'react-router-dom';
import { Box, Typography } from '@mui/material';
import ConfirmationDialog from './ConfirmationDialog';
import { Button as MUIButton } from '@mui/material';
import 'primereact/resources/themes/saga-blue/theme.css';
import 'primereact/resources/primereact.min.css';
import 'primeicons/primeicons.css';

const UserTable = () => {
  const [users, setUsers] = useState([]);
  const [, setAlert] = useState({ visible: false, type: '', msg: '' });
//  const [first, setFirst] = useState(0);
  const [rows, setRows] = useState(10);
  const [totalRecords, setTotalRecords] = useState(0);
  const [videoDialogOpen, setVideoDialogOpen] = useState(false);
  const [videoUrl, setVideoUrl] = useState('');
//  const [filters, setFilters] = useState({});
//  const [sortField, setSortField] = useState(null);
//  const [sortOrder, setSortOrder] = useState(null);
  const [dialogOpen, setDialogOpen] = useState(false); // Manage dialog visibility
  const [selectedUserId, setSelectedUserId] = useState(null);
  const navigate = useNavigate();

  const [lazyParameters, setLazyParameters] = useState({
    first: 0,
    rows: 10,
    sortField: "",
    sortOrder: 1,
    filters: null
  });

  const fetchUsers = async (lazyValues = { first: 0, rows: 10, sortField: null, sortOrder: null, filters: null }) => {
    const page = lazyValues.first ?? 0;
    const pageSize = lazyValues.rows ?? 10;
    const sortField = lazyValues.sortField !== null ? lazyValues.sortField : ""
    const sortOrder = lazyValues.sortOrder === 1 ? 'ASC' : 'DESC';

    const filters = lazyValues.filters;

    try {
        const response = await fetch(`/backend/listUsers.php?first=${page}&rows=${pageSize}&sortField=${sortField}&sortOrder=${sortOrder}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(filters),
        });

        const result = await response.json();
        if (result.result === 'SUCCESS') { }
        setUsers(result.data ?? []);
        setTotalRecords(result.totalRecords ?? 0);
    } catch (error) {
        setAlert({
            visible: true,
            type: 'error',
            msg: 'Error fetching users:' + error,
        });
    }
};

  
  useEffect(() => {
    fetchUsers(lazyParameters);
  }, [lazyParameters]);

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

  const onFilterHandler = (e) => {
    setLazyParameters(e);
    console.log('lazyParameters:', JSON.stringify(e));
  };

  const handleShowVideoClick = (user) => {
    if (user.video_id) {
      // Option 1: If your videos folder is publicly accessible:
    setVideoUrl(`/backend/videos/${user.video_id}`); 
    setVideoDialogOpen(true);
    } else {
    alert("This user has no video.");
    }
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
      <Button
        icon="pi pi-video"
        className="p-button-rounded p-button-success"
        onClick={() => handleShowVideoClick(rowData)}
      />
    </div>
  );

  return (
    <Box sx={{ mt: 4, width: '70%', margin: '0 auto' }}>
      <Dialog open={videoDialogOpen} onClose={() => setVideoDialogOpen(false)} maxWidth="md">
        <DialogContent>
          <video width="100%" controls>
            <source src={videoUrl} type="video/mp4" />
            Your browser does not support the video tag.
          </video>
        </DialogContent>
      </Dialog>
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
        filterDisplay="row" dataKey="id"
        onFilter={onFilterHandler}
        filters={lazyParameters.filters}
        value={users}
        lazy
        paginator
        first={lazyParameters.first}
        rows={rows}
        totalRecords={totalRecords}
        onPage={(e) => {
          setLazyParameters({ ...lazyParameters, first: e.first, rows: e.rows });
          setRows(e.rows);
        }}
        onSort={(e) => {
          const { sortField, sortOrder } = e;
          setLazyParameters((prev) => ({ ...prev, sortField, sortOrder }));
        }}
        sortField={lazyParameters.sortField}
        sortOrder={lazyParameters.sortOrder}
        rowsPerPageOptions={[5, 10, 20, 50, 100]}
        responsiveLayout="scroll"
      >
        <Column field="name" sortable header="Name" filter />
        <Column field="email" sortable header="Email" filter />
        <Column field="born_date" sortable header="Born Date" filter />
        <Column field="user_role" sortable header="Role" filter />
        <Column field='action' body={actionTemplate} header="Actions" />
      </DataTable>
    </Box>
    
  );
};

export default UserTable;