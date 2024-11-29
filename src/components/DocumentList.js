import React, { useEffect, useState } from 'react';
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import { Button } from 'primereact/button';

function DocumentList() {
  const [documentList, setDocumentList] = useState([]);
  const [selectedDocument, setSelectedDocument] = useState(null);

  useEffect(() => {
    // Fetch document data from the backend
    fetch('http://localhost/project1/backend/index.php?service=documents')
      .then((response) => response.json())
      .then((data) => setDocumentList(data.body))
      .catch((error) => console.error('Error fetching documents:', error));
  }, []);

  const viewDocument = (documentItem) => {
    setSelectedDocument(documentItem);  // Set the selected document to view
  };

  const actionBodyTemplate = (rowData) => {
    return (
      <Button label="View" icon="pi pi-eye" onClick={() => viewDocument(rowData)} />
    );
  };

  return (
    <div>
      <h2>Document List</h2>
      <DataTable value={documentList} selectionMode="single" onRowSelect={(e) => viewDocument(e.data)}>
        <Column field="title" header="Title" />
        <Column body={actionBodyTemplate} header="Action" />
      </DataTable>

      {selectedDocument && (
        <div>
          <h3>Viewing: {selectedDocument.title}</h3>
          <iframe src={selectedDocument.url} title={selectedDocument.title} width="600" height="400" />
        </div>
      )}
    </div>
  );
}

export default DocumentList;
