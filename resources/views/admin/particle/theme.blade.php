<style>
  :root {
    --pri: #008d8d;
    --pri-lt: #00b5b5;
    --pri-dk: #006666;
    --pri-tint: #e0f7f7;
    --pri-muted: #b2dfdf;
    --sb-from: #f4fafa;
    --sb-to: #e8f4f4;
    --nb-from: #008d8d;
    --nb-to: #006d6d;
    --bg: #f0f6f6;
    --sb-w: 225px;
    --sb-mini: 4.6rem;
    --nb-h: 57px;
    --radius: 10px;
    --shadow: 0 4px 24px rgba(0, 141, 141, 0.1);
    --text-dark: #0d2e2e;
    --text-mid: #2a5050;
    --text-soft: #5a8080;
    --border: #c8e6e6;
  }

  .page-hero {
    background: linear-gradient(135deg, #006666 0%, #008d8d 55%, #00a8a8 100%);
    padding: 1.6rem 2rem 4.2rem;
    position: relative;
    overflow: hidden;
  }

  .page-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1px, transparent 1px);
    background-size: 26px 26px;
  }

  .page-hero .orb {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 65%);
    top: -60px;
    right: 40px;
  }

  .page-hero h1 {
    font-family: "Playfair Display", serif;
    font-size: 1.45rem;
    font-weight: 800;
    color: #fff;
    margin: 0 0 0.25rem;
    position: relative;
    z-index: 2;
  }

  .page-hero p {
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.82rem;
    margin: 0;
    position: relative;
    z-index: 2;
  }

  .pull-card {
    margin-top: -2.4rem;
    position: relative;
    z-index: 10;
    padding: 0 1.5rem 2rem;
  }

  .form-card,
  .main-card,
  .filter-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 6px 32px rgba(0, 141, 141, 0.1);
    border: 1px solid #d0eded;
    overflow: hidden;
  }

  .form-card-head,
  .main-card-head,
  .filter-card-head {
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid #e4f0f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
    background: #f9fdfd;
  }

  .form-card-title,
  .main-card-title,
  .filter-card-title {
    font-size: 0.92rem;
    font-weight: 800;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .form-card-title i,
  .main-card-title i,
  .filter-card-title i { color: var(--pri); }

  .form-card-body,
  .main-card-body,
  .filter-card-body { padding: 1.5rem; }

  .page-actions {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    flex-wrap: wrap;
  }

  .page-note {
    font-size: 0.78rem;
    color: var(--text-soft);
  }

  .btn-create,
  .btn-submit {
    background: linear-gradient(135deg, var(--pri-dk), var(--pri-lt));
    color: #fff !important;
    border-radius: 8px;
    padding: 0.5rem 1.15rem;
    font-size: 0.82rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    border: none;
    box-shadow: 0 3px 12px rgba(0, 141, 141, 0.25);
  }

  .btn-cancel {
    background: #f0fafa;
    color: var(--text-mid);
    border: 1.5px solid var(--border);
    border-radius: 8px;
    padding: 0.52rem 1.2rem;
    font-size: 0.86rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
  }

  .count-badge,
  .role-chip,
  .soft-badge {
    background: var(--pri-tint);
    color: var(--pri-dk);
    border: 1px solid var(--pri-muted);
    border-radius: 6px;
    padding: 0.12rem 0.6rem;
    font-size: 0.72rem;
    font-weight: 700;
  }

  .sb-active,
  .sb-completed {
    background: #e0f7f7;
    color: #006666;
    border: 1px solid #a0d8d8;
    border-radius: 20px;
    padding: 0.2rem 0.75rem;
    font-size: 0.72rem;
    font-weight: 700;
    display: inline-block;
  }

  .sb-inactive,
  .sb-cancelled {
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #fecdd3;
    border-radius: 20px;
    padding: 0.2rem 0.75rem;
    font-size: 0.72rem;
    font-weight: 700;
    display: inline-block;
  }

  .alert-success-custom {
    background: #e0f7f7;
    border-left: 4px solid var(--pri);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    color: var(--pri-dk);
    font-size: 0.84rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .user-name-cell {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    min-width: 0;
  }

  .user-list-avatar {
    width: 34px;
    height: 34px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #d0eded;
    flex-shrink: 0;
    background: #fff;
  }

  .summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
  }

  .stack-card {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .empty-state {
    padding: 2rem 1rem;
    text-align: center;
    color: var(--text-soft);
  }

  .empty-state i {
    display: block;
    margin-bottom: 0.65rem;
    font-size: 1.5rem;
    color: var(--pri);
  }

  .modal-content {
    border: 1px solid #d0eded;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 18px 45px rgba(0, 141, 141, 0.18);
  }

  .modal-header,
  .modal-footer {
    border-color: #e4f0f0;
    background: #f9fdfd;
  }

  .modal-title {
    font-weight: 800;
    color: var(--text-dark);
  }

  @media (max-width: 1199.98px) {
    .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  }

  @media (max-width: 767.98px) {
    .page-hero { padding: 1.35rem 1rem 3.85rem; }
    .pull-card { padding: 0 0.75rem 1.25rem; }
    .form-card-body,
    .main-card-body,
    .filter-card-body,
    .form-card-head,
    .main-card-head,
    .filter-card-head { padding-left: 1rem; padding-right: 1rem; }
    .summary-grid { grid-template-columns: 1fr; }
  }
</style>
