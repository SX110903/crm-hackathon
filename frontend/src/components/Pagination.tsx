import React from 'react';

interface PaginationProps {
  currentPage: number;
  totalPages: number;
  onPageChange: (page: number) => void;
}

const Pagination: React.FC<PaginationProps> = ({ currentPage, totalPages, onPageChange }) => {
  if (totalPages <= 1) return null;

  const pages: (number | string)[] = [];
  const delta = 2;

  for (let i = 1; i <= totalPages; i++) {
    if (
      i === 1 ||
      i === totalPages ||
      (i >= currentPage - delta && i <= currentPage + delta)
    ) {
      pages.push(i);
    } else if (
      (i === currentPage - delta - 1 && i > 1) ||
      (i === currentPage + delta + 1 && i < totalPages)
    ) {
      pages.push('...');
    }
  }

  // Deduplicate ellipsis entries
  const deduped: (number | string)[] = [];
  pages.forEach(p => {
    if (deduped[deduped.length - 1] !== '...' || p !== '...') {
      deduped.push(p);
    }
  });

  return (
    <div className="pagination">
      <button
        className="pagination-btn"
        disabled={currentPage === 1}
        onClick={() => onPageChange(currentPage - 1)}
      >
        &laquo; Prev
      </button>

      {deduped.map((page, idx) =>
        page === '...' ? (
          <span key={`ellipsis-${idx}`} className="pagination-ellipsis">
            &hellip;
          </span>
        ) : (
          <button
            key={page}
            className={`pagination-btn ${currentPage === page ? 'pagination-btn-active' : ''}`}
            onClick={() => onPageChange(page as number)}
          >
            {page}
          </button>
        )
      )}

      <button
        className="pagination-btn"
        disabled={currentPage === totalPages}
        onClick={() => onPageChange(currentPage + 1)}
      >
        Next &raquo;
      </button>
    </div>
  );
};

export default Pagination;
