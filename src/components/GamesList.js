// src/components/GamesList.js
import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import { Button as PrimeButton } from 'primereact/button';
import { Dialog, DialogContent, DialogActions, Button, TextField, Typography } from '@mui/material';

import 'primereact/resources/themes/saga-blue/theme.css';
import 'primereact/resources/primereact.min.css';
import 'primeicons/primeicons.css';

const GamesList = () => {
  const [games, setGames] = useState([]);
  const [totalRecords, setTotalRecords] = useState(0);
  const [lazyParams, setLazyParams] = useState({
    first: 0,
    rows: 10,
    sortField: '',
    sortOrder: 1,
    filters: {}
  });

  // Simple search
  const [searchTitle, setSearchTitle] = useState('');

  // For preview
  const [previewDialogOpen, setPreviewDialogOpen] = useState(false);
  const [selectedUrl, setSelectedUrl] = useState('');

  const navigate = useNavigate();

  const fetchGames = async (params) => {
    try {
      const page      = params.first ?? 0;
      const pageSize  = params.rows  ?? 10;
      const sortField = params.sortField || '';
      const sortOrder = params.sortOrder === 1 ? 'ASC' : 'DESC';

      const filters = { title: searchTitle };

      const response = await fetch(`/backend/listGames.php?first=${page}&rows=${pageSize}&sortField=${sortField}&sortOrder=${sortOrder}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(filters),
      });
      const result = await response.json();
      if (result.result === 'SUCCESS') {
        setGames(result.data);
        setTotalRecords(result.totalRecords);
      } else {
        console.error(result.message);
      }
    } catch (error) {
      console.error('Error fetching games:', error);
    }
  };

  useEffect(() => {
    fetchGames(lazyParams);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [lazyParams]);

  const onPage = (e) => {
    const newParams = { ...lazyParams, first: e.first, rows: e.rows };
    setLazyParams(newParams);
  };
  const onSort = (e) => {
    setLazyParams((prev) => ({ ...prev, sortField: e.sortField, sortOrder: e.sortOrder }));
  };

  const onFilter = () => {
    const newParams = { ...lazyParams, first: 0 };
    setLazyParams(newParams);
  };

  // CRUD
  const handleAddClick = () => {
    navigate('/game-form');
  };
  const handleEditClick = (rowData) => {
    navigate(`/game-form?id=${rowData.id}`);
  };
  const handleDeleteClick = async (rowData) => {
    if (!window.confirm(`Delete "${rowData.title}"?`)) return;
    try {
      const response = await fetch(`/backend/deleteGame.php?id=${rowData.id}`, { method: 'POST' });
      const result = await response.json();
      if (result.result === 'SUCCESS') {
        fetchGames(lazyParams);
      } else {
        alert(result.message || 'Error deleting game');
      }
    } catch (error) {
      console.error('Delete error:', error);
    }
  };

  // PREVIEW 
  const handlePreviewClick = (rowData) => {
    setSelectedUrl(rowData.iframe_url); 
    setPreviewDialogOpen(true);
  };
  const closePreviewDialog = () => {
    setPreviewDialogOpen(false);
    setSelectedUrl('');
  };

  const actionBodyTemplate = (rowData) => (
    <div className="p-buttonset">
      <PrimeButton
        icon="pi pi-eye"
        className="p-button-rounded p-button-info mr-2"
        onClick={() => handlePreviewClick(rowData)}
      />
      <PrimeButton
        icon="pi pi-pencil"
        className="p-button-rounded p-button-warning mr-2"
        onClick={() => handleEditClick(rowData)}
      />
      <PrimeButton
        icon="pi pi-trash"
        className="p-button-rounded p-button-danger"
        onClick={() => handleDeleteClick(rowData)}
      />
    </div>
  );

  return (
    <div style={{ margin: '1rem' }}>
      <Typography variant="h4">Games List</Typography>
      
      <div style={{ display: 'flex', gap: '1rem', marginBottom: '1rem' }}>
        <TextField
          label="Search Title"
          value={searchTitle}
          onChange={(e) => setSearchTitle(e.target.value)}
        />
        <Button variant="contained" onClick={onFilter}>Search</Button>
      </div>

      <Button variant="contained" onClick={handleAddClick} style={{ marginBottom: '1rem' }}>
        Add New Game
      </Button>

      <DataTable
        value={games}
        paginator
        rows={lazyParams.rows}
        first={lazyParams.first}
        totalRecords={totalRecords}
        onPage={onPage}
        sortField={lazyParams.sortField}
        sortOrder={lazyParams.sortOrder}
        onSort={onSort}
        dataKey="id"
        responsiveLayout="scroll"
      >
        <Column field="title" header="Title" sortable />
        <Column field="description" header="Description" />
        <Column field="created_at" header="Created" sortable />
        <Column body={actionBodyTemplate} header="Actions" />
      </DataTable>

      <Dialog open={previewDialogOpen} onClose={closePreviewDialog} maxWidth="md" fullWidth>
        <DialogContent>
          {selectedUrl ? (
            <iframe
              title="Game Preview"
              src={selectedUrl}
              style={{ width: '100%', height: '500px', border: 'none' }}
              allowFullScreen
            />
          ) : (
            <p>No game URL selected</p>
          )}
        </DialogContent>
        <DialogActions>
          <Button onClick={closePreviewDialog}>Close</Button>
        </DialogActions>
      </Dialog>
    </div>
  );
};

export default GamesList;
