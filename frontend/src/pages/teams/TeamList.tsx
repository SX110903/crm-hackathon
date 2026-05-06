import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { getTeams, deleteTeam, Team } from '../../api/teams';
import Pagination from '../../components/Pagination';
import ConfirmModal from '../../components/ConfirmModal';

const TeamList: React.FC = () => {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const [page, setPage] = useState(1);
  const [search, setSearch] = useState('');
  const [searchInput, setSearchInput] = useState('');
  const [deleteTarget, setDeleteTarget] = useState<Team | null>(null);

  const { data, isLoading, isError } = useQuery({
    queryKey: ['teams', page, search],
    queryFn: () => getTeams(page, search),
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => deleteTeam(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['teams'] });
      setDeleteTarget(null);
    },
  });

  const teams: Team[] = data?.data?.data ?? [];
  const totalPages: number = data?.data?.last_page ?? 1;
  const total: number = data?.data?.total ?? 0;

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    setSearch(searchInput);
    setPage(1);
  };

  return (
    <div className="list-page">
      <div className="page-header">
        <div>
          <h1 className="page-heading">Teams</h1>
          <p className="page-subheading">{total} total teams</p>
        </div>
        <Link to="/teams/new" className="btn btn-primary">+ Add Team</Link>
      </div>

      <div className="list-controls">
        <form onSubmit={handleSearch} className="search-form">
          <input
            type="text"
            className="form-input search-input"
            placeholder="Search teams..."
            value={searchInput}
            onChange={e => setSearchInput(e.target.value)}
          />
          <button type="submit" className="btn btn-secondary">Search</button>
          {search && (
            <button type="button" className="btn btn-outline" onClick={() => { setSearchInput(''); setSearch(''); setPage(1); }}>
              Clear
            </button>
          )}
        </form>
      </div>

      {isLoading && <div className="loading-container"><div className="spinner"></div></div>}
      {isError && <div className="alert alert-error">Failed to load teams.</div>}

      {!isLoading && !isError && (
        <>
          <div className="table-container">
            <table className="table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Max Members</th>
                  <th>Leader ID</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {teams.length === 0 ? (
                  <tr><td colSpan={5} className="table-empty">No teams found.</td></tr>
                ) : (
                  teams.map((t, idx) => (
                    <tr key={t.id}>
                      <td>{(page - 1) * 15 + idx + 1}</td>
                      <td><strong>{t.name}</strong></td>
                      <td>{t.max_members}</td>
                      <td>{t.leader_id ?? '—'}</td>
                      <td>
                        <div className="action-buttons">
                          <button className="btn btn-sm btn-secondary" onClick={() => navigate(`/teams/${t.id}/edit`)}>Edit</button>
                          <button className="btn btn-sm btn-danger" onClick={() => setDeleteTarget(t)}>Delete</button>
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
          title="Delete Team"
          message={`Are you sure you want to delete "${deleteTarget.name}"?`}
          onConfirm={() => deleteMutation.mutate(deleteTarget.id)}
          onCancel={() => setDeleteTarget(null)}
          confirmLabel={deleteMutation.isPending ? 'Deleting...' : 'Delete'}
        />
      )}
    </div>
  );
};

export default TeamList;
