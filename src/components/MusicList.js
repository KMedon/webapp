// src/components/MusicList.js
import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

// PrimeReact components (optional) 
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import { Button as PrimeButton } from 'primereact/button';

// MUI components
import { Dialog, DialogContent, DialogActions, Button, TextField, Typography, Box } from '@mui/material';

import 'primereact/resources/themes/saga-blue/theme.css';
import 'primereact/resources/primereact.min.css';
import 'primeicons/primeicons.css';

/**
 * MusicList component
 * Lists music from /backend/listMusic.php
 * Allows searching by title/artist
 * Shows an audio preview in a Dialog
 * Has actions for Edit / Delete
 */
const MusicList = () => {
  const [musicItems, setMusicItems] = useState([]);
  const [totalRecords, setTotalRecords] = useState(0);
  const [lazyParams, setLazyParams] = useState({
    first: 0,
    rows: 10,
    sortField: '',
    sortOrder: 1,  // 1 = ASC, -1 = DESC
    filters: {}
  });

  // Search form states
  const [searchTitle, setSearchTitle] = useState('');
  const [searchArtist, setSearchArtist] = useState('');

  // Preview dialog
  const [previewDialogOpen, setPreviewDialogOpen] = useState(false);
  const [selectedUrl, setSelectedUrl] = useState('');

  const navigate = useNavigate();

  // 1) Fetch music from backend
  const fetchMusic = async (params) => {
    try {
      const page      = params.first ?? 0;
      const pageSize  = params.rows  ?? 10;
      const sortField = params.sortField || '';
      const sortOrder = params.sortOrder === 1 ? 'ASC' : 'DESC';

      // Build filter object 
      const filters = {
        title:  searchTitle,
        artist: searchArtist
      };

      const response = await fetch(`/backend/music.php?first=${page}&rows=${pageSize}&sortField=${sortField}&sortOrder=${sortOrder}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(filters),
      });

      const result = await response.json();
      if (result.result === 'SUCCESS') {
        setMusicItems(result.data);
        setTotalRecords(result.totalRecords);
      } else {
        console.error(result.message || 'Error listing music');
      }
    } catch (error) {
      console.error('Error fetching music:', error);
    }
  };

  // 2) Handle pagination/sorting
  const onPage = (e) => {
    const newParams = { ...lazyParams, first: e.first, rows: e.rows };
    setLazyParams(newParams);
  };
  const onSort = (e) => {
    const { sortField, sortOrder } = e;
    setLazyParams((prev) => ({ ...prev, sortField, sortOrder }));
  };

  // 3) Trigger fetch on mount or any time lazyParams changes
  useEffect(() => {
    fetchMusic(lazyParams);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [lazyParams]);

  // 4) Search button 
  const onFilter = () => {
    // Reset to first page, then fetch
    const newParams = { ...lazyParams, first: 0 };
    setLazyParams(newParams);
  };

  // 5) CRUD actions
  const handleAddClick = () => {
    // navigate to music form (INSERT mode)
    navigate('/music-form');
  };

  const handleEditClick = (rowData) => {
    // navigate to music form with ?id= rowData.id
    navigate(`/music-form?id=${rowData.id}`);
  };

  const handleDeleteClick = async (rowData) => {
    if (!window.confirm(`Delete "${rowData.title}"?`)) return;

    try {
      const response = await fetch(`/backend/deleteMusic.php?id=${rowData.id}`, {
        method: 'POST'
      });
      const result = await response.json();
      if (result.result === 'SUCCESS') {
        // Refresh the list
        fetchMusic(lazyParams);
      } else {
        alert(result.message || 'Error deleting music');
      }
    } catch (err) {
      console.error('Error deleting music:', err);
    }
  };

  // 6) Audio Preview
  const handlePreviewClick = (rowData) => {
    // file_path = e.g. 'mus_12345.mp3'
    // We assume /backend/music/ folder is accessible
    if (rowData.file_path) {
      setSelectedUrl(`/backend/music/${rowData.file_path}`);
      setPreviewDialogOpen(true);
    } else {
      alert('No audio file for this record.');
    }
  };
  const closePreviewDialog = () => {
    setPreviewDialogOpen(false);
    setSelectedUrl('');
  };

  // 7) DataTable action buttons
  const actionBodyTemplate = (rowData) => {
    return (
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
  };

  return (
    <div style={{ margin: '1rem' }}>
      <Typography variant="h4" gutterBottom>Music List</Typography>

      {/* Search Inputs */}
      <div style={{ display: 'flex', gap: '1rem', marginBottom: '1rem' }}>
        <TextField 
          label="Search Title" 
          value={searchTitle} 
          onChange={(e) => setSearchTitle(e.target.value)} 
        />
        <TextField 
          label="Search Artist" 
          value={searchArtist} 
          onChange={(e) => setSearchArtist(e.target.value)} 
        />
        <Button variant="contained" onClick={onFilter}>
          Search
        </Button>
      </div>

      {/* Add New Music Button */}
      <Button variant="contained" color="primary" onClick={handleAddClick} style={{ marginBottom: '1rem' }}>
        Add New Music
      </Button>

      {/* DataTable */}
      <DataTable
        value={musicItems}
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
        <Column field="artist" header="Artist" sortable />
        <Column field="file_path" header="File Path" />
        <Column field="created_at" header="Created" sortable />
        <Column body={actionBodyTemplate} header="Actions" />
      </DataTable>

      {/* Audio Preview Dialog */}
      <Dialog open={previewDialogOpen} onClose={closePreviewDialog} maxWidth="sm" fullWidth>
      <DialogContent>
          {selectedUrl ? (
            <Box display="flex" flexDirection="column" alignItems="center" gap={2}>
              <Typography variant="h6">Audio Preview</Typography>
              <audio controls style={{ width: '100%' }} src={selectedUrl}>
                Your browser does not support the audio element.
              </audio>
            </Box>
          ) : (
            <Typography>No audio selected</Typography>
          )}
        </DialogContent>
        <DialogActions>
          <Button onClick={closePreviewDialog}>Close</Button>
        </DialogActions>
      </Dialog>
    </div>
  );
};

export default MusicList;
