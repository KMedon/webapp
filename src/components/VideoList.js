// src/components/VideoList.js
import React, { useEffect, useState } from 'react';
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import { Button as PrimeButton } from 'primereact/button';
import { Dialog, DialogContent, DialogActions, Button, TextField, Typography } from '@mui/material';

// If using the same style as your UserTable
import 'primereact/resources/themes/saga-blue/theme.css';
import 'primereact/resources/primereact.min.css';
import 'primeicons/primeicons.css';

const VideoList = () => {
  const [videoItems, setVideoItems] = useState([]);
  const [totalRecords, setTotalRecords] = useState(0);
  const [lazyParams, setLazyParams] = useState({
    first: 0,
    rows: 10,
    sortField: '',
    sortOrder: 1,  // 1 = ASC, -1 = DESC
    filters: {}    // { title: 'Foo' }
  });

  // Search Form state
  const [searchTitle, setSearchTitle] = useState('');
  const [searchArtist, setSearchArtist] = useState('');

  // For embedded audio preview (small modal)
  const [previewDialogOpen, setPreviewDialogOpen] = useState(false);
  const [selectedUrl, setSelectedUrl] = useState('');

  // FETCH VIDEO
  const fetchVideo = async (params) => {
    try {
      const page      = params.first ?? 0;
      const pageSize  = params.rows ?? 10;
      const sortField = params.sortField ?? '';
      const sortOrder = params.sortOrder === 1 ? 'ASC' : 'DESC';

      // Build filter object for POST
      const filters = {
        title:  searchTitle,
        artist: searchArtist
      };

      const response = await fetch(
        `/backend/videos.php?first=${page}&rows=${pageSize}&sortField=${sortField}&sortOrder=${sortOrder}`,
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(filters),
        }
      );
      const result = await response.json();
      if (result.result === 'SUCCESS') {
        setVideoItems(result.data);
        setTotalRecords(result.totalRecords);
      }
    } catch (error) {
      console.error('Error fetching video:', error);
    }
  };

  function getYouTubeEmbedUrl(originalUrl) {
    // Extract the VIDEO_ID from ?v=VIDEO_ID
    // Basic approach (not bulletproof):
    const urlObj = new URL(originalUrl);
    const videoId = urlObj.searchParams.get('v');
    return `https://www.youtube.com/embed/${videoId}`;
  }
  

  // ON PAGE/SORT/FILTER
  const onPage = (e) => {
    const newParams = { ...lazyParams, first: e.first, rows: e.rows };
    setLazyParams(newParams);
  };
  const onSort = (e) => {
    const { sortField, sortOrder } = e;
    setLazyParams((prev) => ({ ...prev, sortField, sortOrder }));
  };
  const onFilter = () => {
    // We’ll reset to first page
    const newParams = { ...lazyParams, first: 0 };
    setLazyParams(newParams);
  };

  useEffect(() => {
    fetchVideo(lazyParams);
  }, [lazyParams]);

  // CRUD Actions
  const handleAddClick = () => {
    window.location.href = '/video-form';
  };

  const handleEditClick = (rowData) => {
    window.location.href = `/video-form?id=${rowData.id}`;
  };

  const handleDeleteClick = async (rowData) => {
    if (!window.confirm(`Delete "${rowData.title}"?`)) return;

    try {
      const response = await fetch(`/backend/deleteVideos.php?id=${rowData.id}`, {
        method: 'POST'
      });
      const result = await response.json();
      if (result.result === 'SUCCESS') {
        // Refresh list
        fetchVideo(lazyParams);
      } else {
        alert(result.message || 'Error deleting video');
      }
    } catch (err) {
      console.error('Error deleting video:', err);
    }
  };

  const handlePreviewClick = (rowData) => {
    setSelectedUrl(rowData.url);
    setPreviewDialogOpen(true);
  };

  const closePreviewDialog = () => {
    setPreviewDialogOpen(false);
    setSelectedUrl('');
  };

  // Datatable action column
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
      <Typography variant="h4">Video List</Typography>

      {/* Simple search form */}
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
        <Button variant="contained" onClick={onFilter}>Search</Button>
      </div>

      <Button variant="contained" color="primary" onClick={handleAddClick}>
        Add New Video
      </Button>

      <DataTable
        value={videoItems}
        paginator
        rows={lazyParams.rows}
        first={lazyParams.first}
        totalRecords={totalRecords}
        onPage={onPage}
        sortField={lazyParams.sortField}
        sortOrder={lazyParams.sortOrder}
        onSort={onSort}
        // If you want to do advanced "onFilter", you can adapt from your UserTable
        responsiveLayout="scroll"
        dataKey="id"
      >
        <Column field="title" header="Title" sortable />
        <Column field="artist" header="Artist" sortable />
        <Column field="url" header="URL" />
        <Column field="created_at" header="Created" sortable />
        <Column body={actionBodyTemplate} header="Actions" />
      </DataTable>

      {/* Audio Preview Dialog */}
      <Dialog open={previewDialogOpen} onClose={closePreviewDialog}>
      <DialogContent>
        {selectedUrl ? (
          <iframe
            width="560"
            height="315"
            src={getYouTubeEmbedUrl(selectedUrl)}
            title="YouTube video player"
            frameBorder="0"
            allow="autoplay; encrypted-media"
            allowFullScreen
          ></iframe>
        ) : (
          <p>No URL selected</p>
        )}
      </DialogContent>
        <DialogActions>
          <Button onClick={closePreviewDialog}>Close</Button>
        </DialogActions>
      </Dialog>
    </div>
  );
};

export default VideoList;
