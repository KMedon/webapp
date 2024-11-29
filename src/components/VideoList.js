import React, { useEffect, useState } from 'react';
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import { Button } from 'primereact/button';

function VideoList() {
  const [videoList, setVideoList] = useState([]);
  const [selectedVideo, setSelectedVideo] = useState(null);

  useEffect(() => {
    // Fetch video data from the backend
    fetch('/index.php?service=videos')
      .then((response) => response.json())
      .then((data) => setVideoList(data.body))
      .catch((error) => console.error('Error fetching videos:', error));
  }, []);

  const playVideo = (videoItem) => {
    setSelectedVideo(videoItem);  // Set the selected video to play
  };

  const actionBodyTemplate = (rowData) => {
    return (
      <Button label="Play" icon="pi pi-play" onClick={() => playVideo(rowData)} />
    );
  };

  return (
    <div>
      <h2>Video List</h2>
      <DataTable value={videoList} selectionMode="single" onRowSelect={(e) => playVideo(e.data)}>
        <Column field="title" header="Title" />
        <Column body={actionBodyTemplate} header="Action" />
      </DataTable>

      {selectedVideo && (
        <div>
          <h3>Now Playing: {selectedVideo.title}</h3>
          <video width="600" height="400" controls autoPlay>
            <source src={selectedVideo.url} type="video/mp4" />
            Your browser does not support the video element.
          </video>
        </div>
      )}
    </div>
  );
}

export default VideoList;
