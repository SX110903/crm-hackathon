import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { getParticipants, deleteParticipant, Participant } from '../../api/participants';
import Pagination from '../../components/Pagination';
import ConfirmModal from '../../components/ConfirmModal';

const ParticipantList: React.FC = () => {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const [page, setPage] = useState(1);
  const [search, setSearch] = useState('');
  const [searchInput, setSearchInput] = useState('');
  const [deleteTarget, setDeleteTarget] = useState<Participant | null>(null);

  const { data, isLoading, isError } = useQuery({
    queryKey: ['participants', page, search],
    queryFn: () => getParticipants(page, search),
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => deleteParticipant(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['participants'] });
      setDeleteTarget(null);
    },
  });

  const participants: Participant[] = data?.data?.data ?? [];
  const totalPages: number = data?.data?.last_page ?? 1;
  const total: number = data?.data?.total ?? 0;

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    setSearch(searchInput);
    setPage(1);
  };

  const handleClearSearch = () => {
    setSearchInput('');
    setSearch('');
    setPage(1);
  };

  return (
    <div className="list-page">
      <div className="page-header">
        <div>
          <h1 className="page-heading">Participants</h1>
          <p className="page-subheading">{total} total participants</p>
        </div>
        <Link to="/participants/new" className="btn btn-primary">
          + Add Participant
        </Link>
      </div>

      <div className="list-controls">
        <form onSubmit={handleSearch} className="search-form">
          <input
            type="text"
            className="form-input search-input"
            placeholder="Search by name, email, university..."
            value={searchInput}
            onChange={e => setSearchInput(e.target.value)}
          />
          <button type="submit" className="btn btn-secondary">Search</button>
          {search && (
            <button type="button" className="btn btn-outline" onClick={handleClearSearch}>
              Clear
            </button>
          )}
        </form>
      </div>

      {isLoading && (
        <div className="loading-container">
          <div className="spinner"></div>
        </div>
      )}

      {isError && (
        <div className="alert alert-error">Failed to load participants.</div>
      )}

      {!isLoading && !isError && (
        <>
          <div className="table-container">
            <table className="table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>University</th>
                  <th>Major</th>
                  <th>Year</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {participants.length === 0 ? (
                  <tr>
                    <td colSpan={7} className="table-empty">
                      No participants found.
                    </td>
                  </tr>
                ) : (
                  participants.map((p, idx) => (
                    <tr key={p.id}>
                      <td>{(page - 1) * 15 + idx + 1}</td>
                      <td>
                        <strong>{p.first_name} {p.last_name}</strong>
                      </td>
                      <td>{p.email}</td>
                      <td>{p.university ?? '—'}</td>
                      <td>{p.major ?? '—'}</td>
                      <td>{p.year_of_study ?? '—'}</td>
                      <td>
                        <div className="action-buttons">
                          <button
                            className="btn btn-sm btn-secondary"
                            onClick={() => navigate(`/participants/${p.id}/edit`)}
                          >
                            Edit
                          </button>
                          <button
                            className="btn btn-sm btn-danger"
                            onClick={() => setDeleteTarget(p)}
                          >
                            Delete
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>

          <Pagination currentPage={page} totalPages={totalPages} onPageChange={setPage} />
        </>
      )}

      {deleteTarget && (
        <ConfirmModal
          title="Delete Participant"
          message={`Are you sure you want to delete "${deleteTarget.first_name} ${deleteTarget.last_name}"? This action cannot be undone.`}
          onConfirm={() => deleteMutation.mutate(deleteTarget.id)}
          onCancel={() => setDeleteTarget(null)}
          confirmLabel={deleteMutation.isPending ? 'Deleting...' : 'Delete'}
        />
      )}
    </div>
  );
};

export default ParticipantList;
