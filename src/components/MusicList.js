import React, { useEffect, useState } from 'react';
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import { Button } from 'primereact/button';

function MusicList() {
  const [musicList, setMusicList] = useState([]);
  const [selectedMusic, setSelectedMusic] = useState(null);

  useEffect(() => {
    // Fetch music data from the backend
    fetch('http://localhost/project1/backend/index.php?service=music')
      .then((response) => response.json())
      .then((data) => setMusicList(data.body))
      .catch((error) => console.error('Error fetching music:', error));
  }, []);

  const playMusic = (musicItem) => {
    setSelectedMusic(musicItem);  // Set the selected music to play
  };

  const actionBodyTemplate = (rowData) => {
    return (
      <Button label="Play" icon="pi pi-play" onClick={() => playMusic(rowData)} />
    );
  };

  return (
    <div>
      <h2>Music List</h2>
      <DataTable value={musicList} selectionMode="single" onRowSelect={(e) => playMusic(e.data)}>
        <Column field="title" header="Title" />
        <Column body={actionBodyTemplate} header="Action" />
      </DataTable>

      {selectedMusic && (
        <div>
          <h3>Now Playing: {selectedMusic.title}</h3>
          <audio controls autoPlay>
            <source src={selectedMusic.url} type="audio/mpeg" />
            Your browser does not support the audio element.
          </audio>
        </div>
      )}
    </div>
  );
}

export default MusicList;
